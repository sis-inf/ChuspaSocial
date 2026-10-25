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
 * Detail page of a book listing of the marketplace.
 *
 * Shows the photos, the description, the courses, the seller and the price of
 * a listing. The management buttons (mark as sold and delete) are shown, and
 * accepted, only to whoever can manage the listing: its owner or a site admin.
 *
 * @package    local_chuspasocial
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_chuspasocial\local\listing_service;
use local_chuspasocial\local\persistent\listing;

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();

$context = context_system::instance();
require_capability('local/chuspasocial:view', $context);

$marketplaceenabled = get_config('local_chuspasocial', 'enablemarketplace');
if ($marketplaceenabled !== false && empty($marketplaceenabled)) {
    throw new moodle_exception('marketplacedisabled', 'local_chuspasocial');
}

$listing = new listing($id);

$title = format_string($listing->get('title'), true, ['context' => $context]);
$available = ($listing->get('status') === 'available');
$sellerid = (int) $listing->get('userid');

$canmanage = ($sellerid === (int) $USER->id) || is_siteadmin();

$pageurl = new moodle_url('/local/chuspasocial/listing.php', ['id' => $id]);
$marketplaceurl = new moodle_url('/local/chuspasocial/marketplace.php');

$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_chuspasocial'));
$PAGE->navbar->add(get_string('books', 'local_chuspasocial'), $marketplaceurl);
$PAGE->navbar->add($title);

if ($action === 'sold' || $action === 'delete') {
    if (!$canmanage) {
        throw new moodle_exception('nopermissions', 'error', $pageurl, $action);
    }

    if ($action === 'sold') {
        require_sesskey();
        if ($available) {
            $DB->update_record('local_chuspasocial_listing', (object) [
                'id' => $id,
                'status' => 'sold',
                'timemodified' => time(),
            ]);
        }
        redirect($pageurl);
    }

    if (!$confirm) {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(
            get_string('deletecheck', '', $title),
            new moodle_url($pageurl, ['action' => 'delete', 'confirm' => 1]),
            $pageurl
        );
        echo $OUTPUT->footer();
        exit;
    }

    require_sesskey();
    $transaction = $DB->start_delegated_transaction();
    $DB->delete_records('local_chuspasocial_lstcourse', ['listingid' => $id]);
    $DB->delete_records('local_chuspasocial_listing', ['id' => $id]);
    $transaction->allow_commit();

    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'local_chuspasocial', 'photos', $id);
    $fs->delete_area_files($context->id, 'local_chuspasocial', 'listing', $id);

    redirect($marketplaceurl);
}

$photos = [];
$files = get_file_storage()->get_area_files($context->id, 'local_chuspasocial', 'photos', $id, 'filename', false);
foreach ($files as $file) {
    if (!$file->is_valid_image()) {
        continue;
    }
    $photos[] = moodle_url::make_pluginfile_url(
        $context->id,
        'local_chuspasocial',
        'photos',
        $id,
        $file->get_filepath(),
        $file->get_filename()
    );
}

$courserecords = $DB->get_records_sql(
    "SELECT c.*
       FROM {course} c
       JOIN {local_chuspasocial_lstcourse} lc ON lc.courseid = c.id
      WHERE lc.listingid = :listingid
   ORDER BY c.fullname",
    ['listingid' => $id]
);
$courses = [];
foreach ($courserecords as $course) {
    if (core_course_category::can_view_course_info($course)) {
        $courses[] = format_string($course->fullname, true, ['context' => context_course::instance($course->id)]);
    }
}

$seller = core_user::get_user($sellerid);
$sellername = ($seller && !$seller->deleted) ? fullname($seller) : '';

echo $OUTPUT->header();

echo html_writer::start_tag('article', ['class' => 'local_chuspasocial_listing', 'data-listingid' => $id]);
echo html_writer::tag('h2', $title, ['class' => 'h3']);
echo html_writer::tag('p', s($listing->get('author')), ['class' => 'text-muted']);

if ($photos) {
    echo html_writer::start_div('d-flex flex-wrap mb-3');
    foreach ($photos as $photourl) {
        echo html_writer::img($photourl, $title, ['class' => 'img-thumbnail mr-2 mb-2', 'style' => 'max-height: 240px;']);
    }
    echo html_writer::end_div();
}

echo html_writer::start_div('mb-3');
echo html_writer::tag(
    'span',
    s(listing_service::format_price((float) $listing->get('price'))),
    ['class' => 'h4 font-weight-bold mr-2']
);
if ($available) {
    echo html_writer::span(get_string('available', 'local_chuspasocial'), 'badge badge-success');
} else {
    echo html_writer::span(get_string('sold', 'local_chuspasocial'), 'badge badge-secondary');
}
echo html_writer::end_div();

$description = $listing->get('description');
if ($description !== null && trim($description) !== '') {
    echo html_writer::div(
        format_text($description, $listing->get('descriptionformat'), ['context' => $context]),
        'mb-3'
    );
}

if ($courses) {
    echo html_writer::start_div('mb-3');
    echo html_writer::span(get_string('courses') . ': ', 'font-weight-bold');
    foreach ($courses as $coursename) {
        echo html_writer::span($coursename, 'badge badge-info mr-1');
    }
    echo html_writer::end_div();
}

echo html_writer::start_div('mb-3');
echo html_writer::tag('h3', get_string('contactseller', 'local_chuspasocial'), ['class' => 'h5']);
if ($sellername !== '') {
    echo html_writer::tag('p', html_writer::link(
        new moodle_url('/user/profile.php', ['id' => $sellerid]),
        $sellername
    ));
}
if ($available && $sellername !== '' && $sellerid !== (int) $USER->id && !empty($CFG->messaging)) {
    echo $OUTPUT->single_button(
        new moodle_url('/message/index.php', ['id' => $sellerid]),
        get_string('message'),
        'get'
    );
}
$externalcontact = $listing->get('externalcontact');
if ($available && $externalcontact !== null && trim($externalcontact) !== '') {
    echo html_writer::tag('p', html_writer::span(get_string('externalcontact', 'local_chuspasocial') . ': ', 'font-weight-bold') .
        s($externalcontact), ['class' => 'mb-1']);
    echo html_writer::tag('p', get_string('externalcontactwarning', 'local_chuspasocial'), ['class' => 'small text-muted']);
}
echo html_writer::end_div();

if ($canmanage) {
    echo html_writer::start_div('d-flex mb-3');
    if ($available) {
        echo html_writer::div($OUTPUT->single_button(
            new moodle_url($pageurl, ['action' => 'sold']),
            get_string('markassold', 'local_chuspasocial')
        ), 'mr-2');
    }
    echo $OUTPUT->render(new single_button(
        new moodle_url($pageurl, ['action' => 'delete']),
        get_string('delete'),
        'get',
        single_button::BUTTON_DANGER
    ));
    echo html_writer::end_div();
}

echo html_writer::end_tag('article');

echo html_writer::link($marketplaceurl, get_string('books', 'local_chuspasocial'));

echo $OUTPUT->footer();