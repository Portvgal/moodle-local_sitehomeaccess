@local @local_sitehomeaccess @javascript
Feature: Configure Site Home access without existing cohorts
  In order to understand how to configure cohort exceptions
  As a site administrator
  I need the settings page to explain when no cohorts are available

  Scenario: The settings page links to cohort management when there are no cohorts
    Given I log in as "admin"
    When I navigate to "Plugins > Local plugins > Site Home access" in site administration
    Then I should see "No cohorts are available."
    And "Manage cohorts" "link" should exist
