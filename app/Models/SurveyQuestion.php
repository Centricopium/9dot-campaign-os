<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id',
        'question',
        'type',
        'options',
        'required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }

    public function getOptionsListAttribute(): array
    {
        return $this->options ?? [];
    }

    public function getIsChoiceQuestionAttribute(): bool
    {
        return in_array($this->type, [
            'radio',
            'checkbox',
            'select',
        ]);
    }

    public function getIsTextQuestionAttribute(): bool
    {
        return in_array($this->type, [
            'text',
            'textarea',
        ]);
    }

    public function getIsRatingQuestionAttribute(): bool
    {
        return $this->type === 'rating';
    }
}