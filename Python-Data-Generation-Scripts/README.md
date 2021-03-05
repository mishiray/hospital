# Python Data Generation Scripts
This python script will automatically generate data for the hospital database.

## Requirements

- Python 3.xx or Python 2.xx
- MySqlMySQL Connector - `mysql-connector-python`
- Names - `names`

## Install requirements
> <b>Note:</b> Make sure you run the bash command in the same directory as `requirements.txt`<br>If you aren't in the directory, run: `cd hospital/Python-Data-Generation-Scripts/`
```bash
$ pip install -r requirements.txt
```
## Setup connection to database
Add `connection.json` to the same directory as `generator.py`<br>Then edit the connection details accordingly.
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
> <b>Note:</b> The `create schema` will overwrite the schema if it already exists.<br>Useful for reseting the whole database
```
>> Create schema hospital
```
### Create table
**eg**: TABLE `patient`
> <b>Note:</b> The `create table` will overwrite the table if it already exists. <br>Useful for reseting the table
```
>> Create table patient
```
### Add doctors
> <b>Note:</b> The number of `doctors` can not exceed the number of `nurses`
```
>> Add 1 doctor
```
### Add receptionists
```
>> Add 15 receptionists
```
### Add room
> <b>Note:</b> The number of `rooms` can not exceed the number of `nurses`
```
>> Add 12 rooms
```
### Add nurse
```
>> Add 14 nurses
```
### Add patient
> <b>Note:</b> There must be at least one `receptionist` before adding a `patient`.
```
>> Add 77 patients
```
### Add appointment
> <b>Note:</b> The number of `appointments` can not exceed the number of `patients`.
```
>> Add 50 appointments
```
### Reset all tables
> <b>Note:</b> This will clear all data in all the tables.<br>It is reccomended you do this whenever you want to run a new simulation.
```
>> Reset tables
```
### End the program
```
>> End
```
