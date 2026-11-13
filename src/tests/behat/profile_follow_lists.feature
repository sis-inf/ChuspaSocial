@local @local_chuspasocial
Feature: Open the lists of followers and followed users from the profile
  In order to know who follows a user and who the user follows
  As a user
  I want the profile counters to open the lists of followers and followed users

  Scenario: The followers counter opens the followers list
    Given I log in as "admin"
    And I visit "/local/chuspasocial/profile.php?id=2"
    When I click on "Followers: 0" "link"
    Then I should see "Followers" in the "h3" "css_element"
    And I should see "Nothing to display"

  Scenario: The following counter opens the following list
    Given I log in as "admin"
    And I visit "/local/chuspasocial/profile.php?id=2"
    When I click on "Following: 0" "link"
    Then I should see "Following" in the "h3" "css_element"
    And I should see "Nothing to display"
