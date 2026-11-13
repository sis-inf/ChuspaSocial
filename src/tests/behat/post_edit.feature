@local @local_chuspasocial @javascript
Feature: Edit an own post on the wall
  In order to correct what I published
  As a student
  I want to edit my own posts and nobody else's

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Curso 1  | CURSO1    | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | CURSO1 | student |
      | student2 | CURSO1 | student |
    And I log in as "student1"
    And I visit "/local/chuspasocial/index.php"
    And I set the field "Post content" to "Texto original de la publicacion"
    And I press "Publish"
    And I log out

  Scenario: The author edits the post and it shows the edited mark
    Given I log in as "student1"
    And I visit "/local/chuspasocial/index.php"
    And I wait "1" seconds
    When I click on "Edit post" "link" in the ".local_chuspasocial_wall > .post-item:first-child" "css_element"
    Then the field "Post content" matches value "Texto original de la publicacion"
    And I set the field "Post content" to "Texto corregido de la publicacion"
    And I press "Publish"
    Then I should see "Texto corregido de la publicacion"
    And I should not see "Texto original de la publicacion"
    And I should see "Edited" in the ".local_chuspasocial_wall > .post-item:first-child" "css_element"

  Scenario: Another user cannot edit the post
    Given I log in as "student2"
    When I visit "/local/chuspasocial/index.php"
    Then I should see "Texto original de la publicacion"
    And I should not see "Edit post" in the ".local_chuspasocial_wall > .post-item:first-child" "css_element"
