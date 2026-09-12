<?php
namespace App\Controllers;
use App\Models\SiswaModel;

class Siswa extends BaseController {
    public function index() {
        $model = new SiswaModel();
        $data['siswa'] = $model->findAll();
        return view('siswa/index', $data);
    }

    public function create() {
        return view('siswa/create');
    }

    public function store() {
        $model = new SiswaModel();
        $model->save($this->request->getPost());
        return redirect()->to('/siswa');
    }

    public function edit($id) {
        $model = new SiswaModel();
        $data['siswa'] = $model->find($id);
        return view('siswa/edit', $data);
    }

    public function update($id) {
        $model = new SiswaModel();
        $model->update($id, $this->request->getPost());
        return redirect()->to('/siswa');
    }

    public function delete($id) {
        $model = new SiswaModel();
        $model->delete($id);
        return redirect()->to('/siswa');
    }
}
