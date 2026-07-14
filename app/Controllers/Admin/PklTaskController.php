<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PklTaskModel;

class PklTaskController extends BaseController
{
    protected $taskModel;

    public function __construct()
    {
        $this->taskModel = new PklTaskModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $tasks = $this->taskModel->getAllWithSiswa($search, $status);

        $data = [
            'title' => 'Master Task PKL',
            'tasks' => $tasks,
            'search' => $search,
            'status' => $status,
        ];

        return view('admin/pkl_task/index', $data);
    }

    public function nonaktifkan($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return redirect()->back()->with('error', 'Task tidak ditemukan');
        }

        $this->taskModel->update($id, ['status' => 'inactive']);

        return redirect()->to('admin/pkl-task')->with('success', 'Task berhasil dinonaktifkan');
    }

    public function aktifkan($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return redirect()->back()->with('error', 'Task tidak ditemukan');
        }

        $this->taskModel->update($id, ['status' => 'active']);

        return redirect()->to('admin/pkl-task')->with('success', 'Task berhasil diaktifkan');
    }

    public function hapus($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return redirect()->back()->with('error', 'Task tidak ditemukan');
        }

        $this->taskModel->delete($id);

        return redirect()->to('admin/pkl-task')->with('success', 'Task berhasil dihapus');
    }

    public function bulkAction()
    {
        $action = $this->request->getPost('bulk_action');
        $ids = $this->request->getPost('ids');

        if (!$action || empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Pilih aksi dan task terlebih dahulu');
        }

        $count = 0;
        foreach ($ids as $id) {
            $task = $this->taskModel->find($id);
            if (!$task) continue;

            match ($action) {
                'nonaktifkan' => $this->taskModel->update($id, ['status' => 'inactive']),
                'aktifkan' => $this->taskModel->update($id, ['status' => 'active']),
                'hapus' => $this->taskModel->delete($id),
                default => null,
            };

            $count++;
        }

        $labels = ['nonaktifkan' => 'dinonaktifkan', 'aktifkan' => 'diaktifkan', 'hapus' => 'dihapus'];
        return redirect()->to('admin/pkl-task')->with('success', $count . ' task berhasil ' . ($labels[$action] ?? $action));
    }
}
