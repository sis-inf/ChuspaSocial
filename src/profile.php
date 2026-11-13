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
 * Profile page of ChuspaSocial.
 *
 * @package    local_chuspasocial
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_chuspasocial\local\follow_service;

$id = required_param('id', PARAM_INT);
$list = optional_param('list', '', PARAM_ALPHA);
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 20;

require_login();

$context = context_system::instance();

require_capability('local/chuspasocial:view', $context);
$PAGE->set_pagelayout('standard');

$user = core_user::get_user($id, '*', MUST_EXIST);
if ($user->deleted) {
    throw new moodle_exception('invaliduserid');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/chuspasocial/profile.php', ['id' => $id]));
$PAGE->set_title(fullname($user));
$PAGE->set_heading(fullname($user));

// Counters: users that follow this user and users followed by this user.
$followers = $DB->count_records_sql(
    "SELECT COUNT(1)
       FROM {local_chuspasocial_follow} f
       JOIN {user} u ON u.id = f.userid
      WHERE f.itemtype = :itemtype
        AND f.itemid = :itemid
        AND u.deleted = 0",
    ['itemtype' => 'user', 'itemid' => $id]
);
$following = $DB->count_records_sql(
    "SELECT COUNT(1)
       FROM {local_chuspasocial_follow} f
       JOIN {user} u ON u.id = f.itemid
      WHERE f.itemtype = :itemtype
        AND f.userid = :userid
        AND u.deleted = 0",
    ['itemtype' => 'user', 'userid' => $id]
);

// Posts visible to the viewer: general wall and the courses the viewer is enrolled in.
$courseids = array_keys(enrol_get_my_courses('id'));
$courseids[] = 0;
[$insql, $params] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'cid');
$params['userid'] = $id;

$posts = $DB->get_records_select(
    'local_chuspasocial_post',
    "userid = :userid AND hidden = 0 AND courseid $insql",
    $params,
    'timecreated DESC, id DESC'
);

echo $OUTPUT->header();

echo $OUTPUT->user_picture($user, ['size' => 100, 'link' => false]);
echo html_writer::tag('h2', s(fullname($user)));
// The counters link to the lists of followers and followed users.
$profileurl = new moodle_url('/local/chuspasocial/profile.php', ['id' => $id]);
echo html_writer::div(
    html_writer::link(
        new moodle_url($profileurl, ['list' => 'followers']),
        get_string('followers', 'local_chuspasocial') . ': ' . $followers
    ) . ' · ' .
    html_writer::link(
        new moodle_url($profileurl, ['list' => 'following']),
        get_string('following', 'local_chuspasocial') . ': ' . $following
    )
);

// Paginated list of followers or followed users (deleted users are filtered by the service).
if ($list === 'followers' || $list === 'following') {
    if ($list === 'followers') {
        $users = follow_service::get_followers($id, $page, $perpage);
        $total = $followers;
    } else {
        $users = follow_service::get_following($id, $page, $perpage);
        $total = $following;
    }

    echo $OUTPUT->heading(get_string($list, 'local_chuspasocial'), 3);

    if ($users) {
        echo html_writer::start_tag('ul', ['class' => 'list-unstyled local-chuspasocial-follow-list']);
        foreach ($users as $listuser) {
            $image = html_writer::empty_tag('img', [
                'src' => $listuser['profileimageurl'],
                'class' => 'rounded-circle mr-2',
                'width' => 35,
                'height' => 35,
                'alt' => '',
            ]);
            $link = html_writer::link(
                new moodle_url('/local/chuspasocial/profile.php', ['id' => $listuser['id']]),
                s($listuser['fullname'])
            );
            echo html_writer::tag('li', $image . $link, ['class' => 'mb-2']);
        }
        echo html_writer::end_tag('ul');
    } else {
        echo $OUTPUT->notification(get_string('nothingtodisplay'), \core\output\notification::NOTIFY_INFO);
    }

    echo $OUTPUT->paging_bar($total, $page, $perpage, new moodle_url($profileurl, ['list' => $list]));
}

foreach ($posts as $post) {
    echo html_writer::start_div('local-chuspasocial-post');
    echo html_writer::div(format_text($post->content, $post->format, ['context' => $context]));
    echo html_writer::div(userdate($post->timecreated));
    echo html_writer::end_div();
}

echo $OUTPUT->footer();