<?php

namespace Modules\Graveyard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemporaryGraveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $temporaryGraves = [
            [ 'grave_id'=>'2', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'1', 'status'=>null, 'oldno'=>'II-A-1', 'last_burial_date'=>'17-07-2023: Santan  Fernandes', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'3', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'2', 'status'=>null, 'oldno'=>'II-A-2', 'last_burial_date'=>'29-12-2024: Maria Michael  Rodrigues', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'4', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'3', 'status'=>null, 'oldno'=>'II-A-3', 'last_burial_date'=>'24-01-2024: Sylvia Miranda Miranda', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'5', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'4', 'status'=>null, 'oldno'=>'II-A-4', 'last_burial_date'=>'08-05-2023: Felix Joseph Fernandes', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'6', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'5', 'status'=>null, 'oldno'=>'II-A-5', 'last_burial_date'=>'31-12-2021: Salvasaun Diago Fernandes', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'7', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'6', 'status'=>null, 'oldno'=>'II-A-6', 'last_burial_date'=>'20-04-2022: Eustaquio Henrique  Dias', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'8', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'7', 'status'=>null, 'oldno'=>'II-A-7', 'last_burial_date'=>'25-04-2022: Julie Anthony D\'Souza', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'9', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'8', 'status'=>null, 'oldno'=>'II-A-8', 'last_burial_date'=>'28-03-2024: Snedden Andrew  Netto', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'10', 'section'=>'4O', 'row_no'=>'8', 'grave_no'=>'9', 'status'=>null, 'oldno'=>'II-A-9', 'last_burial_date'=>'04-12-2021: Francis John Mendonca', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'11', 'section'=>'4O', 'row_no'=>'7', 'grave_no'=>'1', 'status'=>null, 'oldno'=>'II-B-1', 'last_burial_date'=>'23-03-2022: Grace Henry Saldanha', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'12', 'section'=>'4O', 'row_no'=>'7', 'grave_no'=>'2', 'status'=>null, 'oldno'=>'II-B-2', 'last_burial_date'=>'27-03-2022: Joseph Jacob Fernandes', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'13', 'section'=>'4O', 'row_no'=>'7', 'grave_no'=>'3', 'status'=>null, 'oldno'=>'II-B-3', 'last_burial_date'=>'14-02-2024: Jonita  Michael Rebello', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'14', 'section'=>'4O', 'row_no'=>'7', 'grave_no'=>'4', 'status'=>null, 'oldno'=>'II-B-4', 'last_burial_date'=>'26-10-2021: Luke Stephen Pereira', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'15', 'section'=>'4O', 'row_no'=>'7', 'grave_no'=>'5', 'status'=>null, 'oldno'=>'II-B-5', 'last_burial_date'=>'10-11-2021: Anthony Sunny Rodricks', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
            [ 'grave_id'=>'16', 'section'=>'4O', 'row_no'=>'7', 'grave_no'=>'6', 'status'=>null, 'oldno'=>'II-B-6', 'last_burial_date'=>'23-07-2023: Michael Martin Pereira', 'owner'=>'NA', 'contact_no'=>null, 'member_id'=>null, 'remarks'=>null, 'plot_size'=>null, 'is_active'=>null, 'created_by'=>null, 'updated_by'=>null], 
        ];

        foreach ($temporaryGraves as $temporaryGrave) {
            // Extract just the date from last_burial_date (format: "DD-MM-YYYY: Name")
            $lastBurialDate = null;
            if (isset($temporaryGrave['last_burial_date']) && $temporaryGrave['last_burial_date']) {
                $dateStr = explode(':', $temporaryGrave['last_burial_date'])[0];
                try {
                    $lastBurialDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dateStr))->format('Y-m-d');
                } catch (\Exception $e) {
                    // If date parsing fails, set to null
                    $lastBurialDate = null;
                }
            }

            // Map the data to match the database schema
            $mappedData = [
                'grave_id' => $temporaryGrave['grave_id'],
                'section' => $temporaryGrave['section'],
                'row_no' => $temporaryGrave['row_no'],
                'grave_no' => $temporaryGrave['grave_no'],
                'status' => $temporaryGrave['status'] ?? 'available',
                'oldno' => $temporaryGrave['oldno'],
                'last_burial_date' => $lastBurialDate,
                'duration_months' => 18, // Default duration for temporary graves
                'owner_name' => $temporaryGrave['owner'] ?? null, // Map 'owner' to 'owner_name'
                'contact_no' => $temporaryGrave['contact_no'] ?? null,
                'member_id' => $temporaryGrave['member_id'],
                'remarks' => $temporaryGrave['remarks'],
                'plot_size' => $temporaryGrave['plot_size'],
                'is_active' => $temporaryGrave['is_active'] ?? true,
                'created_by' => $temporaryGrave['created_by'],
                'updated_by' => $temporaryGrave['updated_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Use updateOrInsert to avoid duplicate entries
            DB::table('temporary_graves')->updateOrInsert(
                ['grave_id' => $temporaryGrave['grave_id']], // Search criteria
                $mappedData
            );
        }
    }
}