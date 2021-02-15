<p align="center">
  <img src="https://www.multisafepay.com/img/multisafepaylogo.svg" width="400px" position="center">
</p>

# MultiSafepay refund script

Easily refund multiple MultiSafepay orders using this command-line tool.

## Installation
Place the two files in a place where PHP can be run

## Usage
### CSV file

1. Import the file `refund.csv` in a spreadsheet editor like Excel and fill in all the data you want to refund
2. Export the file as .csv and make sure the delimiter is set to ","

The required values are:

* order_id
* amount
* description

![CSV file](docs/images/csv-file.png)

### Executing the script

1. Place the files `refund.csv` and `refund.php` in the same directory
2. In this directory, execute the following command-line instruction:
```shell
php refund.php <api_key>
```

## Output
Upon execution, refund requests for all transactions in `refund.csv` are processed by MultiSafepay. The result of every request is written to a JSON file.

![CLI output](docs/images/cli-output.png)

## Support
You can create issues on our repository. If you need any additional help or support, please contact <a href="mailto:integration@multisafepay.com">integration@multisafepay.com</a>
