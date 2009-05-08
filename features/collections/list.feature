Feature: Listing Collections
  In order to obtain a list of collection resources
  As a web service consumer
  I want to see a list of collections in various formats
  
  Scenario: Success
    When I request /collections.php in JSON format
    Then it should be successful

  Scenario: JSON format
    When I request /collections.php in JSON format
    Then I should get a list of collections in JSON format
    And I should see the full list of collections