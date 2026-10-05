<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformationItem;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        $items = InformationItem::where('wedding_id', $this->getWedding()->id)
            ->orderBy('display_order')
            ->get();

        return view('admin.information', ['items' => $items, 'wedding' => $this->getWedding()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'icon' => 'nullable|string|max:10',
            'display_order' => 'nullable|integer|min:0',
        ]);

        InformationItem::create([
            'wedding_id' => $this->getWedding()->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'icon' => $validated['icon'] ?? '📌',
            'display_order' => $validated['display_order'] ?? 0,
            'is_published' => true,
        ]);

        return back()->with('success', 'Information item added.');
    }

    public function update(Request $request, InformationItem $item)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'icon' => 'nullable|string|max:10',
            'display_order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $item->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'icon' => $validated['icon'] ?? $item->icon,
            'display_order' => $validated['display_order'] ?? $item->display_order,
            'is_published' => $request->has('is_published'),
        ]);

        return back()->with('success', 'Information item updated.');
    }

    public function destroy(InformationItem $item)
    {
        $item->delete();
        return back()->with('success', 'Information item deleted.');
    }

    public function togglePublished(InformationItem $item)
    {
        $item->update(['is_published' => !$item->is_published]);
        return back()->with('success', $item->is_published ? 'Published.' : 'Unpublished.');
    }
}
