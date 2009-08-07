Feature: Listing Collections
  In order to obtain a list of collection resources
  As a web service consumer
  I want to see a list of collections in various formats
  
  Scenario: Success
    When I request /index.php?q=/collections in JSON format
    Then it should be successful
    And I should get the full list of collections in JSON format
