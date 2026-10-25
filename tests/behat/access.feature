@local_sitehomeaccess @javascript
Feature: Control browser access to Site Home
  In order to keep Site Home available to the intended audience
  As a site administrator
  I need to control its visibility with a default policy and cohort exceptions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                  |
      | allowed  | Allowed   | User     | allowed@example.invalid |
      | denied   | Denied    | User     | denied@example.invalid  |
    And the following "cohorts" exist:
      | name           | idnumber      |
      | Allowed cohort | allowedcohort |
    And the following "cohort members" exist:
      | user    | cohort       |
      | allowed | allowedcohort |

  Scenario: Default Hidden removes Site Home and redirects a denied user
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Site Home access" in site administration
    And I set the following fields to these values:
      | Restrict access to Site Home | 1                                                        |
      | Who can access Site Home?    | Only members of selected cohorts and permitted system roles |
    And I press "Save changes"
    And I log out
    And I log in as "denied"
    Then I should not see "Home" in the ".primary-navigation" "css_element"
    When I am on site homepage
    Then I should see "Site Home is not available for your account."
    And I should see "Dashboard" in the "page-header" "region"

  Scenario: A selected cohort member can access Site Home under Default Hidden
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Site Home access" in site administration
    And I set the following fields to these values:
      | Restrict access to Site Home | 1                                                        |
      | Who can access Site Home?    | Only members of selected cohorts and permitted system roles |
      | Cohorts                      | Allowed cohort (System)                                  |
    And I press "Save changes"
    And I log out
    And I log in as "allowed"
    Then I should see "Home" in the ".primary-navigation" "css_element"
    When I am on site homepage
    Then I should not see "Site Home is not available for your account."

