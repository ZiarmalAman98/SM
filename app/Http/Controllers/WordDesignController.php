<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\SimpleType\Jc;

class WordDesignController extends Controller
{
    /**
     * A) RECOMMENDED: Fill and download from your Word template (pixel-perfect).
     * If your template has placeholders like ${school_name}, ${class}, etc.,
     * we replace them here. If not, this simply streams the same design as-is.
     */
    public function downloadFromTemplate(Request $request)
    {
        $templatePath = public_path('templates/shuqa_maktab_placeholders.docx');

        if (!file_exists($templatePath)) {
            abort(404, "Template not found at: {$templatePath}");
        }

        $processor = new TemplateProcessor($templatePath);

        // Singles
        $processor->setValue('school_name', $request->input('school_name', 'مکتب خصوصی پاراپامیزاد'));
        $processor->setValue('class',       $request->input('class', 'صنف ۷'));
        $processor->setValue('subject',     $request->input('subject', 'ریاضی'));
        $processor->setValue('invigilator', $request->input('invigilator', 'نگران'));
        $processor->setValue('exam_title',  $request->input('exam_title', 'امتحان چهارونیم ماهه'));

        // Loop data
        $students = [
            ['no' => 1, 'student_name' => 'احمد',  'father_name' => 'کریم', 'score' => 95],
            ['no' => 2, 'student_name' => 'فاطمه', 'father_name' => 'رحیم', 'score' => 88],
            ['no' => 3, 'student_name' => 'عمر',   'father_name' => 'لطیف', 'score' => 90],
        ];

        // ✅ Clone the row that contains ${no}; must exist exactly once and be inside a table row
        $processor->cloneRow('no', count($students));

        foreach ($students as $i => $st) {
            $idx = $i + 1;
            $processor->setValue("no#{$idx}",            $st['no']);
            $processor->setValue("student_name#{$idx}",  $st['student_name']);
            $processor->setValue("father_name#{$idx}",   $st['father_name']);
            $processor->setValue("score#{$idx}",         $st['score']);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'docx');
        $processor->saveAs($tmp);
        return response()->download($tmp, 'shuqa_maktab.docx')->deleteFileAfterSend(true);
    }


    /**
     * B) OPTIONAL: Build programmatically (no template).
     * Useful when you want to draw the background + “short PC” image via code.
     * Put your images in storage/app/public and run: php artisan storage:link
     */
    public function buildFromScratch(Request $request)
    {
        $phpWord = new PhpWord();

        // Better defaults for Dari/Persian: language + RTL paragraphs
        $phpWord->getSettings()->setThemeFontLang(new Language('fa-IR'));
        $phpWord->setDefaultFontName('Vazirmatn'); // Fallbacks: Tahoma, XB Zar, B Nazanin
        $phpWord->setDefaultFontSize(14);

        $section = $phpWord->addSection([
            // A4 portrait, minimal margins to let background cover the page
            'pageSizeW'  => 11906, // twips
            'pageSizeH'  => 16838, // twips
            'marginTop'    => 720, // 0.5"
            'marginRight'  => 720,
            'marginBottom' => 720,
            'marginLeft'   => 720,
        ]);

        // --- Background image (full page, behind text)
        $bgPath = storage_path('app/public/bg.png'); // put your background here
        if (file_exists($bgPath)) {
            // Absolute positioned image behind text
            $section->addImage($bgPath, [
                'width' => 800,               // tune sizes to your asset
                'height' => null,              // keep aspect ratio
                'positioning' => 'absolute',
                'posHorizontal' => 'left',
                'posHorizontalRel' => 'page',
                'posVertical'   => 'top',
                'posVerticalRel' => 'page',
                'behindText'    => true,
            ]);
        }

        // --- “Short PC” image on the page
        $pcPath = storage_path('app/public/pc-short.png'); // your short PC image
        if (file_exists($pcPath)) {
            $section->addImage($pcPath, [
                'width' => 320, // short/compact look
                'positioning' => 'absolute',
                'posHorizontal' => 'center',
                'posHorizontalRel' => 'page',
                'posVertical' => 'center',
                'posVerticalRel' => 'page',
                'behindText' => false,
            ]);
        }

        // --- Optional heading text (RTL, right-aligned)
        $section->addText(
            'امتحان چهارونیم ماهه',
            ['bold' => true, 'size' => 18, 'name' => 'Vazirmatn'],
            ['rtl' => true, 'alignment' => Jc::RIGHT]
        );

        // --- Optional meta line (RTL)
        $meta = sprintf(
            'نگران: %s     صنف: %s     مضمون: %s',
            $request->input('invigilator', '—'),
            $request->input('class', '—'),
            $request->input('subject', '—')
        );
        $section->addText($meta, ['name' => 'Vazirmatn'], ['rtl' => true, 'alignment' => Jc::RIGHT]);

        // Stream as .docx
        $file = tempnam(sys_get_temp_dir(), 'docx');
        IOFactory::createWriter($phpWord, 'Word2007')->save($file);
        return response()->download($file, 'design.docx')->deleteFileAfterSend(true);
    }
}
