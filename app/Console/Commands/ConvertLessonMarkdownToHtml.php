<?php

namespace App\Console\Commands;

use App\Models\LmsLesson;
use Illuminate\Console\Command;
use League\CommonMark\CommonMarkConverter;

class ConvertLessonMarkdownToHtml extends Command
{
    protected $signature = 'lms:convert-markdown';
    protected $description = 'Convert Markdown lesson content to HTML';

    public function handle(): void
    {
        $converter = new CommonMarkConverter([
            'html_input'         => 'allow',
            'allow_unsafe_links' => false,
        ]);

        $count = 0;

        LmsLesson::all()->each(function (LmsLesson $lesson) use ($converter, &$count) {
            $content = $lesson->content;
            // Only convert if it looks like Markdown (not already HTML)
            if ($content && !str_starts_with(ltrim($content), '<')) {
                $lesson->content = $converter->convert($content)->getContent();
                $lesson->save();
                $count++;
                $this->line("  ✓ Converted: {$lesson->title}");
            }
        });

        $this->info("Done. {$count} lesson(s) converted to HTML.");
    }
}
