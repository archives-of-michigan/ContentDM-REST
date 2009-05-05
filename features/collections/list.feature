Feature: Listing Collections
  In order to obtain a list of collection resources
  As a web service consumer
  I want to see a list of collections in various formats

  Scenario: JSON format
    When I go to /collections
    Then I should get a list of collections in JSON format
    And I should see the full list of collections