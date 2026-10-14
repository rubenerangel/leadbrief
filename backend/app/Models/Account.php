<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Contacts\Support\DomainNormalizer;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable(['name', 'domain', 'industry'])]
class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    /**
     * Get the contacts associated with this account
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Normalizes the domain on write; blank values are stored as null so the
     *  unique index allows many accounts without a domain.
     */
    protected function domain(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => blank($value)
                ? null
                : DomainNormalizer::normalize($value),
        );
    }

}
