<?php

namespace App\Services;

use App\Models\StudentMeasurement;
use Illuminate\Support\Facades\DB;

class StudentMeasurementService
{
    public function getAllStudentMeasurements()
    {
        return StudentMeasurement::with('student')->get();
    }

    public function getStudentMeasurementById(int $id)
    {
        return StudentMeasurement::with('student')->findOrFail($id);
    }

    public function createStudentMeasurement(array $data)
    {
        return DB::transaction(function () use ($data) {
            $studentMeasurement = StudentMeasurement::create($data);
            return $studentMeasurement->load('student');
        });
    }

    public function updateStudentMeasurement(StudentMeasurement $studentMeasurement, array $data)
    {
        return DB::transaction(function () use ($studentMeasurement, $data) {
            $studentMeasurement->update($data);
            return $studentMeasurement->load('student');
        });
    }

    public function deleteStudentMeasurement(StudentMeasurement $studentMeasurement)
    {
        return DB::transaction(function () use ($studentMeasurement) {
            $studentMeasurement->delete();
            return true;
        });
    }
}