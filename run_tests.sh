#!/bin/bash

newman run tests/Test_API.User.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Account.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Venue.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Year.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Activity.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Discount.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Category.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Topic.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Registration.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli
newman run tests/Test_API.Payment.postman_collection.json -e tests/Test_API.postman_environment.json --color -r html,cli

