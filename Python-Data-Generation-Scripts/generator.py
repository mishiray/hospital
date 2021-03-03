from typing import Tuple
import mysql.connector as msql
import json
import re
import os
import names
from random import randint
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
    keywords = ADD + '|' + 'doctors'
    kwstripped = re.sub(keywords, '', command)
    count = int(re.search(r"\d+", kwstripped).group())

    query = SQL_Q['insert-into']['doctor']
    mycursor.execute("SELECT * FROM doctor")
    doctors = mycursor.fetchall()
    values = []

    for i in range(count):

        doctor_id = 'HTD-' + str(randint(100, 200)) + \
            '-' + str(randint(100, 120))
        name = names.get_full_name(gender='male')
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

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(f"{mycursor.rowcount} doctor(s) was inserted."))


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

        except Exception as e:

            print(e)
            continue
