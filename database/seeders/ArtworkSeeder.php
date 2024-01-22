<?php

namespace Database\Seeders;

use App\Supports\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ArtworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (file_exists(storage_path('app/public/artworks/')) && is_dir(storage_path('app/public/artworks/'))) {
            array_map('unlink', glob(storage_path('app/public/artworks/') . '/*.*'));
            rmdir(storage_path('app/public/artworks/'));
        }

        mkdir(storage_path('app/public/artworks/'));

        $users = \App\models\User::whereIn('name', ['artist1', 'artist2', 'artist3', 'artist4', 'artist5'])->get();

        foreach ($users as $user) {

            $path1 = 'artworks/' . Str::random() . '.jpg';
            $path2 = 'artworks/' . Str::random() . '.jpg';
            $path3 = 'artworks/' . Str::random() . '.jpg';

            File::copy(public_path('dummy/images/image-1.jpg'), storage_path('app/public/' . $path1));
            File::copy(public_path('dummy/images/image-2.jpg'), storage_path('app/public/' . $path2));
            File::copy(public_path('dummy/images/image-3.jpg'), storage_path('app/public/' . $path3));

            \App\Models\Artwork::factory()->create([
                'user_id' => $user->id,
                'path' => $path1,
            ]);

            \App\Models\Artwork::factory()->create([
                'user_id' => $user->id,
                'path' => $path2,
            ]);

            \App\Models\Artwork::factory()->create([
                'user_id' => $user->id,
                'path' => $path3,
            ]);
        }
    }
}
