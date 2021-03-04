from typing import Tuple
import mysql.connector as msql
import json
import re
import os
import names
from random import (randint, choice)
import datetime
import base64

LOG = "[LOG] {}"
SPECIL_FIELDS = [
    "physiology",
    "dermatology",
    "neurology",
    "optimology"
]

if 'Python-Data-Generation-Scripts' in os.listdir():
    os.chdir('Python-Data-Generation-Scripts')

# Load connection.json
with open('connection.json') as json_data:
    CONN = json.load(json_data)

# Load sql-query.json
with open('sql-query.json') as json_data:
    SQL_Q = json.load(json_data)

# COMMANDS
CREATE = r"((C|c)reate|CREATE)"
TABLE = r"((T|t)able|TABLE)"
SCHEMA = r"((S|s)chema|SCHEMA)"
ADD = r"((A|a)dd|ADD)"
IN = r"in"
CREATE_TABLE = f"{CREATE} {TABLE} \w+ {IN} {SCHEMA} \w+"
CREATE_SCHEMA = f"{CREATE} {SCHEMA} \w+"
ADD_DOCTOR = f"{ADD} \d+ docto(r|rs)"
ADD_RECEPTIONIST = f"{ADD} \d+ receptionis(t|ts)"
ADD_ROOM = f"{ADD} \d+ roo(m|ms)"
ADD_NURSE = f"{ADD} \d+ nurs(e|es)"
ADD_PATIENT = f"{ADD} \d+ patien(t|ts)"
YES = r"(y|Y|yes|Yes|YES)"
NO = r"(n|N|no|No|NO)"


def initialize_DB(mycursor):
    '''
    Creates all necessary schema and tables if they do not already exist
    '''

    # Fetch all databases
    mycursor.execute("SHOW DATABASES")
    schemas = [x[0] for x in mycursor.fetchall()]

    if SQL_Q['schema'] not in schemas:

        mycursor.execute("DROP SCHEMA IF EXISTS `hospital`")
        mycursor.execute('\n'.join(SQL_Q['create-schema']))
        print(LOG.format(f"Created schema '{SQL_Q['schema']}'"))

    else:

        print(LOG.format(f"Schema '{SQL_Q['schema']}' exists."))

    # Fetch all tables in hospital
    mycursor.execute("SHOW TABLES")
    tables = [x[0] for x in mycursor.fetchall()]

    for table in SQL_Q['tables']:

        if table not in tables:

            query = '\n'.join(SQL_Q['create-table'][table])
            mycursor.execute(
                f"DROP TABLE IF EXISTS `{SQL_Q['schema']}`.`{table}`")
            mycursor.execute(query)
            print(LOG.format(f"Created table '{table}'"))

        else:

            print(LOG.format(f"Table '{table}' exists."))


def CREATE_SCHEMA_HANDLER(command):

    command = re.search(CREATE_SCHEMA, command).group()
    keywords = CREATE + '|' + SCHEMA
    schema = re.sub(keywords, '', command).strip()

    if schema != SQL_Q['schema']:

        print(f"Schema '{schema}' is invalid within this scope")
        return

    mycursor.execute("SHOW DATABASES")
    schemas = [x[0] for x in mycursor.fetchall()]

    if schema in schemas:

        if re.search(YES, input(f"[WARNING] Schema '{schema}' already exist.\nDo you wish to override?\n>> ")):

            mycursor.execute(f"DROP SCHEMA IF EXISTS `{schema}`")
            mycursor.execute('\n'.join(SQL_Q['create-schema']))
            print(LOG.format(f"Re-created schema '{SQL_Q['schema']}'"))

        else:

            print(f"[LOG] Aborting creation of schema '{schema}'")

    else:

        mycursor.execute('\n'.join(SQL_Q['create-schema']))
        print(LOG.format(f"Created schema '{SQL_Q['schema']}'"))


def CREATE_TABLE_HANDLER(command):

    command = re.search(CREATE_TABLE, command).group()
    keywords = CREATE + '|' + TABLE + '|' + IN + '|' + SCHEMA
    table, schema = tuple(re.sub(keywords, '', command).strip().split())

    if table not in SQL_Q['tables'] or schema != SQL_Q['schema']:

        print(f"Table '{table}' is invalid within this scope")
        return

    mycursor.execute("SHOW TABLES")
    tables = [x[0] for x in mycursor.fetchall()]

    if table in tables:

        if re.search(YES, input(f"[WARNING] Table '{table}' already exist.\nDo you wish to override?\n>> ")):

            query = '\n'.join(SQL_Q['create-table'][table])
            mycursor.execute(f'DROP TABLE IF EXISTS `{schema}`.`{table}`')
            mycursor.execute(query)
            print(LOG.format(
                f"Re-created Table '{table}' in schema '{schema}'"))

        else:

            print(f"[LOG] Aborting creation of schema '{schema}'")

    else:

        query = '\n'.join(SQL_Q['create-table'][table])
        mycursor.execute(query)
        print(LOG.format(f"Created Table '{table}' in schema '{schema}'"))


def ADD_DOCTOR_HANDLER(command):

    command = re.search(ADD_DOCTOR, command).group()
    keywords = ADD + '|' + 'docto(r|rs)'
    kwstripped = re.sub(keywords, '', command)
    count = int(re.search(r"\d+", kwstripped).group())

    query = SQL_Q['insert-into']['doctor']
    mycursor.execute("SELECT * FROM doctor")
    doctors = mycursor.fetchall()
    values = []

    for i in range(count):

        mycursor.execute("SELECT * FROM nurse WHERE doctor_id IS NULL")

        free_nurses = mycursor.fetchall()

        if free_nurses == None:

            print("[WARNING] There are not enough nurses. Aborting operation")
            break

        doctor_id = 'htd-' + str(randint(100, 200)) + \
            '-' + str(randint(100, 120))
        name = names.get_full_name()
        email = name.replace(' ', '.').lower() + '@doctors.ht.com'

        password = 'password'
        password_bytes = password.encode('ascii')
        base64_bytes = base64.b64encode(password_bytes)
        base64_password = base64_bytes.decode('ascii')

        phone = '+' + str(randint(100, 999)) + '-' + \
            str(randint(100, 999)) + '-' + str(randint(100, 999)) + \
            '-' + str(randint(1000, 9999))
        address = str(randint(10, 99)) + ' ' + \
            names.get_first_name() + ' Street, Lagos, Nigeria.'
        specialization = SPECIL_FIELDS[randint(0, len(SPECIL_FIELDS)-1)]
        dateadded = datetime.datetime.now()

        doctor = (doctor_id, name, email, base64_password, phone,
                  address, specialization, dateadded)

        if doctor_id not in [x[0] for x in doctors]:

            values.append(doctor)
            doctors.append(doctor)
            mycursor.execute(
                f"UPDATE nurse SET doctor_id = '{doctor_id}' WHERE nurse_id = '{choice(free_nurses)[0]}'")
            mydb.commit()

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(
        f"{mycursor.rowcount if mycursor.rowcount > 0 else 0} doctor(s) was inserted."))


def ADD_RECEPTIONIST_HANDLER(command):

    command = re.search(ADD_RECEPTIONIST, command).group()
    keywords = ADD + '|' + 'receptionis(t|ts)'
    kwstripped = re.sub(keywords, '', command)
    count = int(re.search(r"\d+", kwstripped).group())

    query = SQL_Q['insert-into']['receptionist']
    mycursor.execute("SELECT * FROM receptionist")
    receptionists = mycursor.fetchall()
    values = []

    for i in range(count):

        receptionist_id = 'htr-' + str(randint(100, 200)) + \
            '-' + str(randint(100, 120))
        name = names.get_full_name()
        email = name.replace(' ', '.').lower() + '@receptionists.ht.com'

        password = 'password'
        password_bytes = password.encode('ascii')
        base64_bytes = base64.b64encode(password_bytes)
        base64_password = base64_bytes.decode('ascii')

        phone = '+' + str(randint(100, 999)) + '-' + \
            str(randint(100, 999)) + '-' + str(randint(100, 999)) + \
            '-' + str(randint(1000, 9999))
        address = str(randint(10, 99)) + ' ' + \
            names.get_first_name() + ' Street, Lagos, Nigeria.'
        dateadded = datetime.datetime.now()

        receptionist = (receptionist_id, name, email, base64_password, phone,
                        address, dateadded)

        if receptionist_id not in [x[0] for x in receptionists]:

            values.append(receptionist)
            receptionists.append(receptionist)

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(f"{mycursor.rowcount} receptionist(s) was inserted."))


def ADD_ROOM_HANDLER(command):

    command = re.search(ADD_ROOM, command).group()
    keywords = ADD + '|' + 'roo(m|ms)'
    kwstripped = re.sub(keywords, '', command)
    count = int(re.search(r"\d+", kwstripped).group())

    query = SQL_Q['insert-into']['room']
    values = []

    for i in range(count):

        type = str(randint(1, 5))
        status = 0
        dateadded = datetime.datetime.now()

        room = (type, status, dateadded)
        values.append(room)

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(f"{mycursor.rowcount} room(s) was inserted."))


def ADD_NURSE_HANDLER(command):

    command = re.search(ADD_NURSE, command).group()
    keywords = ADD + '|' + 'nurs(e|es)'
    kwstripped = re.sub(keywords, '', command)
    count = int(re.search(r"\d+", kwstripped).group())

    query = SQL_Q['insert-into']['nurse']
    mycursor.execute("SELECT * FROM doctor")
    nurses = mycursor.fetchall()
    values = []

    for i in range(count):

        nurse_id = 'htn-' + str(randint(100, 200)) + \
            '-' + str(randint(100, 120))
        name = names.get_full_name(gender='female')
        email = name.replace(' ', '.').lower() + '@nurses.ht.com'

        phone = '+' + str(randint(100, 999)) + '-' + \
            str(randint(100, 999)) + '-' + str(randint(100, 999)) + \
            '-' + str(randint(1000, 9999))
        address = str(randint(10, 99)) + ' ' + \
            names.get_first_name() + ' Street, Lagos, Nigeria.'
        dateadded = datetime.datetime.now()

        nurse = (nurse_id, name, email, phone,
                 address, dateadded)

        if nurse_id not in [x[0] for x in nurses]:

            values.append(nurse)
            nurses.append(nurse)

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(
        f"{mycursor.rowcount if mycursor.rowcount > 0 else 0} nurse(s) was inserted."))


def ADD_PATIENT_HANDLER(command):

    command = re.search(ADD_PATIENT, command).group()
    keywords = ADD + '|' + 'patien(t|ts)'
    kwstripped = re.sub(keywords, '', command)
    count = int(re.search(r"\d+", kwstripped).group())

    query = SQL_Q['insert-into']['patient']
    mycursor.execute("SELECT * FROM patient")
    patients = mycursor.fetchall()
    mycursor.execute("SELECT * FROM receptionist")
    receptionists = mycursor.fetchall()
    values = []

    if receptionists == [] or receptionists == None:

        raise Exception(
            "[WARNING] There are no receptionists. Aborting operation")

    for i in range(count):

        patient_id = 'htp-' + str(randint(100, 200)) + \
            '-' + str(randint(100, 120))
        phone = '+' + str(randint(100, 999)) + '-' + \
            str(randint(100, 999)) + '-' + str(randint(100, 999)) + \
            '-' + str(randint(1000, 9999))

        name = names.get_full_name()
        receptionist_id = choice(receptionists)[0]
        year = randint(1970, 2020)
        month = randint(1, 12)
        day = randint(1, 29 if month == 2 else 30 if month in [
                      9, 4, 6, 11] else 31)
        dob = datetime.date(year, month, day)
        print(f'DOB: {dob}')
        gender = choice(['male', 'female'])
        email = name.replace(' ', '.').lower() + '@email.com'
        dateadded = datetime.datetime.now()

        patient = (patient_id, receptionist_id, name,
                   dob, gender, phone, email, dateadded)

        if patient_id not in [x[0] for x in patients]:

            values.append(patient)
            patients.append(patient)

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(
        f"{mycursor.rowcount if mycursor.rowcount > 0 else 0} patient(s) was inserted."))


if __name__ == "__main__":

    # Initialize connection to database
    mydb = msql.connect(**CONN)
    mycursor = mydb.cursor()

    initialize_DB(mycursor)

    while True:

        mydb = msql.connect(**CONN)
        mycursor = mydb.cursor()

        command = input('\n>> ')

        try:

            if re.search(CREATE_SCHEMA, command):

                CREATE_SCHEMA_HANDLER(command)
                continue

            elif re.search(CREATE_TABLE, command):

                CREATE_TABLE_HANDLER(command)
                continue

            elif re.search(ADD_DOCTOR, command):

                ADD_DOCTOR_HANDLER(command)
                continue

            elif re.search(ADD_RECEPTIONIST, command):

                ADD_RECEPTIONIST_HANDLER(command)
                continue

            elif re.search(ADD_ROOM, command):

                ADD_ROOM_HANDLER(command)
                continue

            elif re.search(ADD_NURSE, command):

                ADD_NURSE_HANDLER(command)
                continue

            elif re.search(ADD_PATIENT, command):

                ADD_PATIENT_HANDLER(command)
                continue

        except Exception as e:

            print(e)
            continue
