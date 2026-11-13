@local @local_chuspasocial
Feature: Review the context of reported content in the moderation queue
  In order to decide on a report without searching for the content
  As a manager
  I need to see a summary of the reported content and a link to its view

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | student1 | Student   | One      | student1@example.com  |
      | manager1 | Manager   | One      | manager1@example.com  |

    And the following "courses" exist:
      | fullname       | shortname |
      | Social course  | social1   |

    And the following "course enrolments" exist:
      | user     | course  | role           |
      | student1 | social1 | student        |
      | manager1 | social1 | editingteacher |

  Scenario: The manager sees a summary and a link for a reported publication
    Given I log in as "student1"
    And I visit "/local/chuspasocial/index.php"
    And I set the field "Message" to "Publication with reported context"
    And I press "Publish"
    And I should see "Publication with reported context"
    When I click on "Report" "link"
    And I log out

    Given I log in as "manager1"
    When I visit "/local/chuspasocial/moderation.php"
    Then I should see "post #"
    And I should see "Publication with reported context"
    And I should see "View reported content"
    When I click on "View reported content" "link"
    Then I should see "Publication with reported context"
