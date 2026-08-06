<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Menampilkan daftar Position milik company yang sedang login.
     */
    public function index(Request $request)
    {
        $positions = Position::with('department')
            ->where('company_id', $request->user()->company_id)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $positions
        ]);
    }

    /**
     * HR membuat Position baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:20',
            'description'   => 'nullable|string'
        ]);

        $position = Position::create([
            'company_id'    => $request->user()->company_id,
            'department_id' => $request->department_id,
            'name'          => $request->name,
            'code'          => $request->code,
            'description'   => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil dibuat.',
            'data'    => $position
        ], 201);
    }

    /**
     * Menampilkan detail satu Position.
     */
    public function show(Request $request, Position $position)
    {
        // Pastikan position ini milik company user yang sedang login
        if ($position->company_id !== $request->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data'    => $position->load('department')
        ]);
    }

    /**
     * HR mengubah data Position.
     */
    public function update(Request $request, Position $position)
    {
        if ($position->company_id !== $request->user()->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:20',
            'description'   => 'nullable|string'
        ]);

        $position->update($request->only(['department_id', 'name', 'code', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil diperbarui.',
            'data'    => $position
        ]);
    }

    /**
     * HR menghapus data Position.
     */
    public function destroy(Request $request, Position $position)
    {
        if ($position->company_id !== $request->user()->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        // Opsional: Anda bisa menambahkan pengecekan jika position masih dipakai oleh employee, tolak hapus.

        $position->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil dihapus.'
        ]);
    }
}