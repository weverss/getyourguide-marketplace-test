# GetYourGuide Marketplace Test

## Requirements

- Docker (preferable latest stable version)

## Running it

Install composer vendor packages:

`make install`

Spin up the container:

`make up`


Run the cli app:

- Start time: 2017-06-06T12:00
- End time: 2017-06-30T12:00
- Number of travelers: 5

`./console.php retrieve:products 2017-06-01T12:00 2017-06-30T12:00 5`

# Considerations

- I've used Symfony Console components to take advantages of argument input and validations, as well as the Guzzle Client, so I could have more time to focus on the test case.

- The commands validation deserve improvements not done due to lack of time.
