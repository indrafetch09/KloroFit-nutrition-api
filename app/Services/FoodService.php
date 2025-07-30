<?php

namespace App\Services;

use App\Models\UserFood;
use App\Models\SummaryFood;
use App\Models\NutritionLibrary; // <--- Import NutritionLibrary
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\MealType;

class FoodService
{
    /**
     * Create a new food item for a user and update summary.
     *
     * @param  array  $data  (harus menyertakan user_id, nutrition_library_id, meal_type, date)
     * @return \App\Models\UserFood
     * @throws \Exception Jika nutrition_library_id tidak ditemukan
     */

    public function createFood(array $data): UserFood
    {

        // 2. Buat instance model UserFood baru
        $userFood = new UserFood(); // Ganti $food dengan $userFood

        // 3. Isi kolom-kolom model UserFood dengan data dari input
        $userFood->user_id = $data['user_id'];
        $userFood->nutrition_library_id = $data['nutrition_library_id'];
        $userFood->meal_type = $data['meal_type'];
        $userFood->date = $data['date'];

        $userFood->save();

        $userFood->load('nutritionLibrary');

        return $userFood; // Return the saved UserFood model
    }

    /**
     * Update an existing food item for a user and update summary.
     *
     * @param  \App\Models\UserFood  $food
     * @param  array  $data (harus menyertakan nutrition_library_id, meal_type, date - jika berubah)
     * @return \App\Models\UserFood
     * @throws \Exception Jika nutrition_library_id tidak ditemukan
     */
    public function updateFood(UserFood $food, array $data): UserFood
    {
        // Periksa apakah nutrition_library_id berubah
        $oldLibraryItem = $food->nutritionLibrary;
        $newLibraryItem = $oldLibraryItem;

        if (isset($data['nutrition_library_id']) && $data['nutrition_library_id'] !== $food->nutrition_library_id) {
            $newLibraryItem = NutritionLibrary::find($data['nutrition_library_id']);
            if (!$newLibraryItem) {
                throw new \Exception('New nutrition library item not found.');
            }
        }

        // Hitung nutrisi yang akan dikurangkan (dari item lama)
        $nutrientsToSubtract = [
            'calories' => $oldLibraryItem->calories,
            'carbs' => $oldLibraryItem->carbs,
            'protein' => $oldLibraryItem->protein,
            'fat' => $oldLibraryItem->fat,
        ];
        $this->updateDailyFoodSummary($food->user_id, $food->date, $nutrientsToSubtract, 'subtract');

        // Update UserFood
        $food->update([
            'nutrition_library_id' => $newLibraryItem->id, // Gunakan ID yang baru (atau yang lama jika tidak berubah)
            'meal_type' => $data['meal_type'] ?? $food->meal_type,
            'date' => $data['date'] ?? $food->date,
        ]);

        // Hitung nutrisi yang akan ditambahkan (dari item baru atau yang diperbarui)
        $nutrientsToAdd = [
            'calories' => $newLibraryItem->calories,
            'carbs' => $newLibraryItem->carbs,
            'protein' => $newLibraryItem->protein,
            'fat' => $newLibraryItem->fat,
        ];
        $this->updateDailyFoodSummary($food->user_id, $food->date, $nutrientsToAdd, 'add');

        return $food;
    }

    /**
     * Delete a food item and update summary.
     *
     * @param  \App\Models\UserFood  $food
     * @return bool|null
     * 
     */
    public function deleteFood(UserFood $food): ?bool
    {
        // Ambil nutrisi dari library yang terkait sebelum menghapus
        $libraryItem = $food->nutritionLibrary;
        if (!$libraryItem) {
            throw new \Exception('Associated nutrition library item not found for food to be deleted.');
        }

        $nutrientsToRemove = [
            'calories' => $libraryItem->calories,
            'carbs' => $libraryItem->carbs,
            'protein' => $libraryItem->protein,
            'fat' => $libraryItem->fat,
        ];

        $deleted = $food->delete();

        if ($deleted) {
            // Kurangkan nutrisi dari summary food berdasarkan tanggal konsumsi
            $this->updateDailyFoodSummary($food->user_id, $food->date, $nutrientsToRemove, 'subtract');
        }
        return $deleted;
    }

    /**
     * Get all food items for a specific user.
     * Eager load nutritionLibrary for performance.
     *
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFoodsForUserByDate(int $userId, string $date): Collection
    {
        return UserFood::where('user_id', $userId)
            ->whereDate('date', $date)
            ->with('nutritionLibrary')
            ->get();
    }


    public function getAllFoodsForUser(int $userId): Collection
    {
        return UserFood::where('user_id', $userId)
            ->with('nutritionLibrary')
            ->get();
    }

    public function createMultipleFoods(int $userId, array $foodsData)
    {
        $createdFoods = collect(); // Buat collection kosong untuk menampung model yang baru dibuat

        foreach ($foodsData as $foodData) {
            // Gunakan metode mass assignment atau satu per satu
            $food = new UserFood([
                'user_id' => $userId,
                'nutrition_library_id' => $foodData['nutrition_library_id'],
                'meal_type' => $foodData['meal_type'],
                'date' => $foodData['date'],
            ]);
            $food->save();

            // Load relasi agar accessor di model berfungsi dengan benar
            $food->load('nutritionLibrary');

            $createdFoods->push($food);
        }
    }

    /**
     * Get a specific food item by ID for a user.
     * Eager load nutritionLibrary.
     *
     * @param  int  $id
     * @return \App\Models\UserFood|null
     */
    public function getFoodById(int $id): ?UserFood
    {
        return UserFood::with('nutritionLibrary')->find($id);
    }

    /**
     * Get recently added food items for a user for a specific date.
     * Eager load nutritionLibrary.
     *
     * @param  int  $userId
     * @param  string $date (e.g., 'YYYY-MM-DD')
     * @param  int  $limit  Number of recent foods to retrieve
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentFoodsForUser(int $userId, string $date, int $limit = 5): Collection
    {
        return UserFood::where('user_id', $userId)
            ->whereDate('date', $date) // Filter berdasarkan tanggal konsumsi
            ->with('nutritionLibrary') // Eager load relasi
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Update the daily food summary for a user for a specific date.
     *
     * @param  int  $userId
     * @param  string|\Carbon\Carbon  $date
     * @param  array  $foodNutrients ['calories', 'carbs', 'protein', 'fat']
     * @param  string $operation 'add', 'subtract'
     * @return void
     */
    protected function updateDailyFoodSummary(int $userId, $date, array $foodNutrients, string $operation): void
    {
        // Pastikan $date adalah instance Carbon atau string 'YYYY-MM-DD'
        $summaryDate = Carbon::parse($date)->toDateString();

        $summary = SummaryFood::firstOrNew([
            'user_id' => $userId,
            'date' => $summaryDate, // Gunakan tanggal konsumsi
        ]);

        foreach (
            [
                'calories' => 'total_calories',
                'carbs' => 'total_carbs',
                'protein' => 'total_protein',
                'fat' => 'total_fat'
            ] as $foodKey => $summaryKey
        ) {
            $value = $foodNutrients[$foodKey] ?? 0;
            if ($operation === 'add') {
                $summary->{$summaryKey} += $value;
            } elseif ($operation === 'subtract') {
                $summary->{$summaryKey} -= $value;
            }
        }
        $summary->save();
    }
}
