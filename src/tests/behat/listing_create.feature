@local @local_chuspasocial
Feature: Publish a book linked to a course
  In order to share books with other students
  As a student
  I need to publish a book linked to a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | course1   | 0        |
    And the following "course enrolments" exist:
      | user     | course  | role    |
      | student1 | course1 | student |

  @javascript
  Scenario: A student publishes a book linked to a course
    Given I log in as "student1"
    When I visit "/local/chuspasocial/listing_edit.php"
    And I set the field "Title" to "Programming Book"
    And I set the field "Author" to "Test Author"
    And I set the field "Price" to "25"
    And I set the field "Courses" to "Course 1"
    And I press "Save changes"
    Then I should see "Programming Book"
    And I should see "Test Author"
    And I should see "Course 1"
