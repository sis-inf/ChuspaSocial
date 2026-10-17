<?php
namespace local_chuspasocial\local;
defined('MOODLE_INTERNAL') || die();
/**
 * @covers \local_chuspasocial\local\listing_service::delete_listing
 */
class listing_service_test extends \advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }
    public function test_delete_listing_as_author() {
        $generator = $this->getDataGenerator();
        $author = $generator->create_user();   
        $plugin_generator = $generator->get_plugin_generator('local_chuspasocial');
        $listing = $plugin_generator->create_listing(['userid' => $author->id]);
        $this->setUser($author);
        $result = listing_service::delete_listing($listing->id);
        $this->assertTrue($result);
    }
    public function test_delete_listing_as_moderator() {
        $generator = $this->getDataGenerator();
        $author = $generator->create_user();
        $moderator = $generator->create_user();   
        $plugin_generator = $generator->get_plugin_generator('local_chuspasocial');
        $listing = $plugin_generator->create_listing(['userid' => $author->id]);
        $this->setUser($moderator);
        $result = listing_service::delete_listing($listing->id);
        $this->assertTrue($result);
    }
    public function test_delete_listing_removes_links() {
        $generator = $this->getDataGenerator();
        $author = $generator->create_user();   
        $plugin_generator = $generator->get_plugin_generator('local_chuspasocial');
        $listing = $plugin_generator->create_listing(['userid' => $author->id]);
        $this->setUser($author);
        $result = listing_service::delete_listing($listing->id);
        $this->assertTrue($result);
    }
}
