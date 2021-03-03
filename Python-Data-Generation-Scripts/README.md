# Python Data Generation Scripts
This python script will automatically generate data for the hospital database.

## Requirements

- Python 3.xx or Python 2.xx
- MySqlMySQL Connector - `mysql-connector-python`
- Regular expressions - `re`
- Json - `json`

Install requirements
```bash
$ pip install -r requirements.txt
```
Add `connection.json` to the same directory as `generator.py`
```json
{
    "host": "localhost",
    "user": "root",
    "password": "p@$$W0rD",
    "database": "hospital"
}
```
## Usage 
Run `generator.py`
```bash
$ python generator.py
```
### Create schema<br>
**eg**: SCHEMA `hospital`
```
>> Create schema hospital
```
### Create table
**eg**: TABLE `patient` in SCHEMA `hospital`
```
>> Create table patient in schema hospital
```
