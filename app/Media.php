<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Media extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The table name to use for this model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * Generate a UUIDv4 ID when creating a model instance.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Media $media) {
            $media->{$media->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * Get the download URL for a media object.
     *
     * @return string
     */
    public function getDownloadUrl(): string
    {
        return secure_url('/media/download/' . $this->id);
    }
}
