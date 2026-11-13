<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Page to review the open content reports and hide the reported content.
 *
 * Only users with the local/chuspasocial:moderate capability can access it.
 * The hide action is submitted with a sesskey and handled by report_service.
 *
 * @package    local_chuspasocial
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use core\output\notification;
use local_chuspasocial\local\report_service;

require_login();

$context = context_system::instance();
require_capability('local/chuspasocial:moderate', $context);

$pageurl = new moodle_url('/local/chuspasocial/moderation.php');

$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('moderation', 'local_chuspasocial'));
$PAGE->set_heading(get_string('pluginname', 'local_chuspasocial'));

$reportid = optional_param('hide', 0, PARAM_INT);
if ($reportid) {
    require_sesskey();

    report_service::resolve_and_hide($reportid);

    redirect(
        $pageurl,
        get_string('changessaved'),
        null,
        notification::NOTIFY_SUCCESS
    );
}

$reports = $DB->get_records(
    'local_chuspasocial_report',
    ['status' => 'open'],
    'timecreated ASC'
);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('openreports', 'local_chuspasocial'));

if ($reports) {
    $table = new html_table();
    $table->head = [
        get_string('content'),
        get_string('reportreason', 'local_chuspasocial'),
        get_string('user'),
        get_string('date'),
        get_string('actions'),
    ];

    foreach ($reports as $report) {
        $reporter = core_user::get_user($report->reporterid);
        $reportername = $reporter ? s(fullname($reporter)) : get_string('unknownuser');

        $hidebutton = new single_button(
            new moodle_url($pageurl, ['hide' => $report->id]),
            get_string('hidecontent', 'local_chuspasocial'),
            'post'
        );

        // Load the reported item to show a safe summary and a link to its view.
        $summary = null;
        $itemurl = null;
        if ($report->itemtype === 'post') {
            $post = $DB->get_record('local_chuspasocial_post', ['id' => $report->itemid]);
            if ($post) {
                $summary = content_to_text($post->content, $post->contentformat);
                $itemurl = $post->courseid
                    ? new moodle_url('/local/chuspasocial/course.php', ['id' => $post->courseid])
                    : new moodle_url('/local/chuspasocial/index.php');
            }
        } else if ($report->itemtype === 'comment') {
            $comment = $DB->get_record('local_chuspasocial_comment', ['id' => $report->itemid]);
            if ($comment) {
                $summary = content_to_text($comment->content, $comment->contentformat);
                $post = $DB->get_record('local_chuspasocial_post', ['id' => $comment->postid]);
                if ($post) {
                    $itemurl = $post->courseid
                        ? new moodle_url('/local/chuspasocial/course.php', ['id' => $post->courseid])
                        : new moodle_url('/local/chuspasocial/index.php');
                }
            }
        } else if ($report->itemtype === 'listing') {
            $listing = $DB->get_record('local_chuspasocial_listing', ['id' => $report->itemid]);
            if ($listing) {
                $summary = $listing->title;
                $itemurl = new moodle_url('/local/chuspasocial/listing.php', ['id' => $listing->id]);
            }
        }

        $contentcell = s($report->itemtype) . ' #' . $report->itemid;
        if ($summary === null) {
            $contentcell .= html_writer::div(
                get_string('reportedcontentmissing', 'local_chuspasocial'),
                'text-muted'
            );
        } else {
            $contentcell .= html_writer::div(s(shorten_text($summary, 100)));
            if ($itemurl) {
                $contentcell .= html_writer::link(
                    $itemurl,
                    get_string('viewreportedcontent', 'local_chuspasocial')
                );
            }
        }

        $table->data[] = [
            $contentcell,
            s($report->reason),
            $reportername,
            userdate($report->timecreated),
            $OUTPUT->render($hidebutton),
        ];
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(
        get_string('nothingtodisplay'),
        notification::NOTIFY_INFO
    );
}

echo $OUTPUT->footer();
