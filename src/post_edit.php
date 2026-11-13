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
 * Page to edit a post of its own author.
 *
 * Only the author of the post can open this page. The form is protected
 * by the sesskey check of moodleform and the post keeps its course.
 *
 * @package    local_chuspasocial
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_chuspasocial\form\post_form;
use local_chuspasocial\local\persistent\post;
use local_chuspasocial\local\post_service;

$postid = required_param('id', PARAM_INT);

$post = post::get_record(['id' => $postid]);
if (!$post) {
    throw new moodle_exception('invalidrecord', 'error', '', 'local_chuspasocial_post');
}

// The post keeps its course: the context and the return page come from the post.
$courseid = (int) $post->get('courseid');
if ($courseid > 0) {
    require_login(get_course($courseid));
    $context = context_course::instance($courseid);
    $returnurl = new moodle_url('/local/chuspasocial/course.php', ['id' => $courseid]);
} else {
    require_login();
    $context = context_system::instance();
    $returnurl = new moodle_url('/local/chuspasocial/index.php');
}

// Only the author can edit the post, even if the URL is built by hand.
if (!post_service::can_view($post, (int) $USER->id) || (int) $post->get('userid') !== (int) $USER->id) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('editpost', 'local_chuspasocial'));
}

$pageurl = new moodle_url('/local/chuspasocial/post_edit.php', ['id' => $postid]);

$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('editpost', 'local_chuspasocial'));
$PAGE->set_heading(get_string('pluginname', 'local_chuspasocial'));

$form = new post_form($pageurl, [
    'courseid' => $courseid,
    'can_post_announcement' => false,
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    // The course is not read from the form: the post keeps the one it had.
    post_service::update_post(
        $postid,
        $data->content['text'],
        (int) $data->content['format'],
        $data->tags ?? [],
        (int) ($data->images ?? 0)
    );

    redirect($returnurl);
}

// Prefill the form with the current content, tags and images of the post.
$draftitemid = file_get_submitted_draft_itemid('images');
file_prepare_draft_area(
    $draftitemid,
    $context->id,
    'local_chuspasocial',
    'postimage',
    $postid,
    [
        'accepted_types' => ['jpg', 'png', 'webp'],
        'maxfiles' => (int) get_config('local_chuspasocial', 'maxpostimages') ?: 5,
        'subdirs' => false,
    ]
);

$form->set_data([
    'content' => [
        'text' => $post->get('content'),
        'format' => (int) $post->get('contentformat'),
    ],
    'courseid' => $courseid,
    'tags' => core_tag_tag::get_item_tags_array('local_chuspasocial', 'local_chuspasocial_post', $postid),
    'images' => $draftitemid,
]);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('editpost', 'local_chuspasocial'));
$form->display();
echo $OUTPUT->footer();
