# Python Data Generation Scripts
This python script will automatically generate data for the hospital database.

## Requirement

- Python 3.xx or Python 2.xx
<<<<<<< HEAD
- sqlalchemy
=======
- MySql - `mysql`
- Regular expressions - `re`
- Json - `json`
>>>>>>> 421d4b23b3f613eb39eecfa48192ac3c048a1b68

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
<<<<<<< HEAD
=======
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
>>>>>>> 421d4b23b3f613eb39eecfa48192ac3c048a1b68
```