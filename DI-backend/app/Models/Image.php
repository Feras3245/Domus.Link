<?php

namespace App\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class Image extends Model
{
    use HasUlids;

    protected $guarded = [];
    protected string $guard_name = 'api';
    public $timestamps = false;

    protected $hidden = ['owner_id', 'owner_type'];

    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }
    
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function setFileAttribute(UploadedFile $file): void
    {
        $this->file = $file;
    }

    protected function getImageContent(string $size): ?string
    {
        $owner_type = $this->owner_type === Immobilie::class ? 'immobilien' : 'users';
        $path = "images/".$owner_type."/".$this->owner_id."/".$size."/".$this->id.".webp";
        // Check if the file exists on the 'local' disk
        if (Storage::disk('local')->exists($path)) {
            // If found, return the raw binary content of the file
            return Storage::disk('local')->path($path);
        }
        return null;
    }

    protected static function booted()
    {
        static::creating(function (Image $image) {
            $file = $image->file;
            if (!$image->file instanceof UploadedFile) {
                throw new \InvalidArgumentException('An instance of UploadedFile is required to create an Image record.');
            }

            $valid = str_contains($file->getMimeType(), 'image/');
            if (!$valid) {
                throw new \InvalidArgumentException('File Mimetype is not an image.');
            }

            $img = new ImageManager(Driver::class)->read($file);
            $webp_large = $img->scale(1440, 900)->encode(new WebpEncoder(quality: 100));
            $webp_medium = $img->scale(1280, 800)->encode(new WebpEncoder(quality: 100));
            $webp_small = $img->scale(640, 400)->encode(new WebpEncoder(quality: 100));
            $owner_type = $image->owner_type === Immobilie::class ? 'immobilien' : 'users';
            $small_path = "images/".$owner_type."/".$image->owner_id."/small"."/".$image->id.".webp";
            $medium_path = "images/".$owner_type."/".$image->owner_id."/medium"."/".$image->id.".webp";
            $large_path = "images/".$owner_type."/".$image->owner_id."/large"."/".$image->id.".webp";
            Storage::disk('local')->put($small_path, $webp_small);
            Storage::disk('local')->put($medium_path, $webp_medium);
            Storage::disk('local')->put($large_path, $webp_large);
        });

        static::deleting(function (Image $image) {
            $owner_type = $image->owner_type === Immobilie::class ? 'immobilien' : 'users';
            $small_path = "images/".$owner_type."/".$image->owner_id."/small"."/".$image->id.".webp";
            $medium_path = "images/".$owner_type."/".$image->owner_id."/medium"."/".$image->id.".webp";
            $large_path = "images/".$owner_type."/".$image->owner_id."/large"."/".$image->id.".webp";
            Storage::disk('local')->delete($small_path);
            Storage::disk('local')->delete($medium_path);
            Storage::disk('local')->delete($large_path);
        });
    }
}
