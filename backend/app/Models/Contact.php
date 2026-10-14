<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Contacts\Support\EmailNormalizer;

#[Fillable(['account_id', 'first_name', 'last_name', 'email', 'job_title', 'phone'])]
class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory;

    /** 
     * Get the account associated with Contact
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the activities associated with Contact
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the tags associated with Contact
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the Segments associated with Contact
     */
    public function segments(): BelongsToMany
    {
        return $this->belongsToMany(Segment::class);
    }

    /**
     * Interact with contact's email address
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            
            set: fn (string $value) => [
                'email' => trim($value),
                'email_normalized' => EmailNormalizer::normalize($value)
            ],
        );
    }
}
