<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EraportTemplate;

class FixEraportTemplateFieldMap extends Command
{
    protected $signature = 'eraport:fix-fieldmap {template_id=2}';
    protected $description = 'Fix field_map template e-raport agar tersimpan sebagai JSON object (bukan string escaped)';

    public function handle()
    {
        $id = (int)$this->argument('template_id');

        $t = EraportTemplate::find($id);
        if (!$t) {
            $this->error("Template id {$id} tidak ditemukan");
            return Command::FAILURE;
        }

        // ✅ field_map FULL (kamu bisa ganti path background bila beda)
        $fieldMap = [
            "template" => [
                "background" => [
                    "path" => "eraport/templates/page-002.png",
                    "baseSize" => ["width" => 1414, "height" => 2000],
                ],
            ],
            "dataBindings" => [
                "student_name"   => "student.name",
                "student_kelas"  => "student.kelas_label",
                "semester_label" => "semester.label",
                "school_name"    => "school.name",
                "program_title"  => "course.title",
                "platform"       => "course.platform",
                "category"       => "course.category",

                "hadir" => "attendance.summary.hadir",
                "sakit" => "attendance.summary.sakit",
                "izin"  => "attendance.summary.izin",
                "alpha" => "attendance.summary.alpha",

                "avg_project" => "scores.avg_project",
                "logic_ct"    => "scores.logic_ct",
                "creativity"  => "scores.creativity",

                "mentor_note" => "mentor_note.note",
                "verify_url"  => "eraport.verify_url",
            ],
            "fields" => [
                ["key"=>"student_name","type"=>"text","rectPct"=>["x"=>0.41,"y"=>0.125,"w"=>0.53,"h"=>0.05],"style"=>["align"=>"left","valign"=>"middle","fontSize"=>14,"fontWeight"=>700,"lineHeight"=>1.2]],
                ["key"=>"student_kelas","type"=>"text","rectPct"=>["x"=>0.41,"y"=>0.210,"w"=>0.22,"h"=>0.05],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>600,"lineHeight"=>1.2]],
                ["key"=>"semester_label","type"=>"text","rectPct"=>["x"=>0.66,"y"=>0.210,"w"=>0.28,"h"=>0.05],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>700,"lineHeight"=>1.2]],
                ["key"=>"school_name","type"=>"text","rectPct"=>["x"=>0.41,"y"=>0.285,"w"=>0.53,"h"=>0.05],"style"=>["align"=>"left","valign"=>"middle","fontSize"=>14,"fontWeight"=>700,"lineHeight"=>1.2]],
                ["key"=>"program_title","type"=>"text","rectPct"=>["x"=>0.41,"y"=>0.355,"w"=>0.53,"h"=>0.05],"style"=>["align"=>"left","valign"=>"middle","fontSize"=>14,"fontWeight"=>700,"lineHeight"=>1.2]],

                ["key"=>"hadir","type"=>"text","rectPct"=>["x"=>0.41,"y"=>0.490,"w"=>0.12,"h"=>0.06],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>800,"lineHeight"=>1.2]],
                ["key"=>"sakit","type"=>"text","rectPct"=>["x"=>0.54,"y"=>0.490,"w"=>0.12,"h"=>0.06],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>800,"lineHeight"=>1.2]],
                ["key"=>"izin","type"=>"text","rectPct"=>["x"=>0.67,"y"=>0.490,"w"=>0.12,"h"=>0.06],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>800,"lineHeight"=>1.2]],
                ["key"=>"alpha","type"=>"text","rectPct"=>["x"=>0.80,"y"=>0.490,"w"=>0.14,"h"=>0.06],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>800,"lineHeight"=>1.2]],

                ["key"=>"platform","type"=>"text","rectPct"=>["x"=>0.72,"y"=>0.625,"w"=>0.22,"h"=>0.06],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>800,"lineHeight"=>1.2]],
                ["key"=>"category","type"=>"text","rectPct"=>["x"=>0.72,"y"=>0.705,"w"=>0.22,"h"=>0.07],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>800,"lineHeight"=>1.2]],
                ["key"=>"avg_project","type"=>"text","rectPct"=>["x"=>0.72,"y"=>0.790,"w"=>0.22,"h"=>0.07],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>900,"lineHeight"=>1.2]],
                ["key"=>"logic_ct","type"=>"text","rectPct"=>["x"=>0.72,"y"=>0.875,"w"=>0.22,"h"=>0.085],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>900,"lineHeight"=>1.2]],
                ["key"=>"creativity","type"=>"text","rectPct"=>["x"=>0.72,"y"=>0.960,"w"=>0.22,"h"=>0.06],"style"=>["align"=>"center","valign"=>"middle","fontSize"=>14,"fontWeight"=>900,"lineHeight"=>1.2]],

                ["key"=>"mentor_note","type"=>"textarea","rectPct"=>["x"=>0.41,"y"=>0.900,"w"=>0.53,"h"=>0.095],"style"=>["align"=>"left","valign"=>"top","fontSize"=>14,"fontWeight"=>700,"lineHeight"=>1.25]],

                ["key"=>"verify_url","type"=>"qrcode","rectPct"=>["x"=>0.07,"y"=>0.62,"w"=>0.17,"h"=>0.12],"style"=>["align"=>"center","valign"=>"middle"]],
            ],
        ];

        $t->field_map = $fieldMap;  // ✅ karena cast array, ini akan tersimpan benar
        $t->save();

        $this->info("OK: field_map template {$id} sudah diset ulang sebagai JSON object.");
        return Command::SUCCESS;
    }
}
