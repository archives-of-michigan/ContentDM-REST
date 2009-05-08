require 'cucumber/formatters/unicode' # Comment out this line if you don't want Cucumber Unicode support
require 'mechanize'
require 'webrat'

Webrat.configure do |config|
  config.mode = :mechanize
end

def webrat
  @webrat ||= Webrat::MechanizeSession.new
end

# Comment out the next two lines if you're not using RSpec's matchers (should / should_not) in your steps.
require 'spec/expectations'
# require 'webrat/rspec-rails'
