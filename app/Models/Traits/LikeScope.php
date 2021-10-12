<?php

namespace App\Models\Traits;

trait LikeScope
{
    /**
     * @param   \Illuminate\Database\Eloquent\Builder $query
     * @param     $column
     * @param     $value
     * @param     $side
     * @param     $isNotLike
     * @param     $isAnd
     * @return    \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLike($query, $column, $value, $side='both', $isNotLike=false, $isAnd=true)
    {
        $operator=$isNotLike ? 'not like' : 'like';

        $escape_like_str=function($str) {
            $like_escape_char='!';

            return str_replace([$like_escape_char, '%', '_'], [
                $like_escape_char.$like_escape_char,
                $like_escape_char.'%',
                $like_escape_char.'_',
            ], $str);
        };

        switch ($side) {
            case 'none':
                $value=$escape_like_str($value);
                break;
            case 'before':
            case 'left':
                $value="%{$escape_like_str($value)}";
                break;
            case 'after':
            case 'right':
                $value="{$escape_like_str($value)}%";
                break;
            case 'both':
            case 'all':
            default:
                $value="%{$escape_like_str($value)}%";
                break;
        }

        return $isAnd ? $query->where($column, $operator, $value) : $query->orWhere($column, $operator, $value);
    }

    public function scopeOrLike($query, $column, $value, $side='both', $isNotLike=false)
    {
        return $query->like($column, $value, $side, $isNotLike, false);
    }

    public function scopeNotLike($query, $column, $value, $side='both', $isAnd=true)
    {
        return $query->like($column, $value, $side, true, $isAnd);
    }

    public function scopeOrNotLike($query, $column, $value, $side='both')
    {
        return $query->like($column, $value, $side, true, false);
    }
}
