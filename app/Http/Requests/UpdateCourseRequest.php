<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:courses,title,' . $this->course->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'instructor_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'level' => 'required|in:beginner,intermediate,advanced',
            'language' => 'nullable|string|max:10',
            'duration' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,published',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('lang.title') ?? 'Title',
            'description' => __('lang.description') ?? 'Description',
            'category_id' => __('lang.category') ?? 'Category',
            'instructor_id' => __('lang.instructor') ?? 'Instructor',
            'image' => __('lang.image') ?? 'Image',
            'price' => __('lang.price') ?? 'Price',
            'discount_price' => __('lang.discount_price') ?? 'Discount Price',
            'level' => __('lang.level') ?? 'Level',
            'language' => __('lang.language') ?? 'Language',
            'duration' => __('lang.duration') ?? 'Duration',
            'status' => __('lang.status') ?? 'Status',
            'sort_order' => __('lang.sort_order') ?? 'Sort Order',
            'meta_title' => __('lang.meta_title') ?? 'Meta Title',
            'meta_description' => __('lang.meta_description') ?? 'Meta Description',
        ];
    }
}
