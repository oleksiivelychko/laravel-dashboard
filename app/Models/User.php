<?php

namespace App\Models;

use App\Interfaces\Pagination;
use App\Interfaces\UploadImages;
use App\Traits\ACL;
use App\Traits\Asset;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;


/**
 * @method select(string[] $array)
 */
class User extends Authenticatable implements MustVerifyEmail, UploadImages, Pagination
{
    use HasFactory, Notifiable, ACL, Asset;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected string $imageField = 'avatar';

    protected string $imagesFolder = 'avatars';

    protected array $cropPresets = [[150,150]];

    public function getImageField(): string
    {
        return $this->imageField;
    }

    public function getImagesFolder(): string
    {
        return $this->imagesFolder;
    }

    public function getCropPresets(): array
    {
        return $this->cropPresets;
    }

    public function setPasswordAttribute(string $value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getUsernameAttribute(): string
    {
        return $this->attributes['name'] ?: $this->attributes['email'];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }


    public function store($rolesIds=[], $permissionsIds=[], $uploadedAvatar=null): bool
    {
        $saved = $this->save();
        if ($saved) {
            $this->assignRoles($rolesIds);
            $this->assignPermissions($permissionsIds);

            if ($uploadedAvatar instanceof UploadedFile) {
                $filename = $this->uploadImage($uploadedAvatar);
                if ($filename) {
                    $this->{$this->getImageField()} = $filename;
                    $this->save();
                }
            }
        }

        return $saved;
    }

    public function pagination(Request $request, ?string $locale=null): LengthAwarePaginator
    {
        $sortColumn = $request->query('sort', 'id');
        $sortDirection = $request->query('direction', 'asc');

        return $this->select(['id', 'name', 'email', 'created_at'])
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($this->getPaginationLimit());
    }

    public function getPaginationLimit(): int
    {
        return config('settings.schema.pagination_limit', 10);
    }

    public function uploadImages(array $data): void {}
}
