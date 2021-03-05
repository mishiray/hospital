from typing import Tuple
import mysql.connector as msql
import json
import re
import os
import names
from random import (randint, choice)
import datetime
import base64
import pprint

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
CREATE_TABLE = f"{CREATE} {TABLE} \w+"
CREATE_SCHEMA = f"{CREATE} hospital \w+"
ADD_DOCTOR = f"{ADD} \d+ docto(r|rs)"
ADD_RECEPTIONIST = f"{ADD} \d+ receptionis(t|ts)"
ADD_ROOM = f"{ADD} \d+ roo(m|ms)"
ADD_NURSE = f"{ADD} \d+ nurs(e|es)"
ADD_PATIENT = f"{ADD} \d+ patien(t|ts)"
ADD_APPOINTMENT = f"{ADD} \d+ appointmen(t|ts)"
ADD_ADMISSION = f"{ADD} \d+ admissio(n|ns)"
STATUS = r"(S|s)tatus"
RESET_TABLE = r"(R|r)eset tabl(e|es)"
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


def RESET_TABLES():

    for table in SQL_Q['tables']:
        query = '\n'.join(SQL_Q['create-table'][table])
        mycursor.execute(f'DROP TABLE IF EXISTS `hospital`.`{table}`')
        mycursor.execute(query)
        print(LOG.format(
            f"Re-created Table '{table}' in schema 'hospital'"))


def SHOW_STATUS():

    for table in SQL_Q['tables']:
        query = f"SELECT * FROM {table}"
        mycursor.execute(query)
        result = mycursor.fetchall()
        print(LOG.format(
            f"Table '{table}' has {len(result)} records."))


def CREATE_SCHEMA_HANDLER(command):

    command = re.search(CREATE_SCHEMA, command).group()
    keywords = CREATE + '|' + SCHEMA
    schema = re.sub(keywords, '', command).strip()

    if schema != SQL_Q['schema']:

        print(f"Schema 'hospital' is invalid within this scope")
        return

    mycursor.execute("SHOW DATABASES")
    schemas = [x[0] for x in mycursor.fetchall()]

    if schema in schemas:

        if re.search(YES, input(f"[WARNING] Schema 'hospital' already exist.\nDo you wish to override?\n>> ")):

            mycursor.execute(f"DROP SCHEMA IF EXISTS `hospital`")
            mycursor.execute('\n'.join(SQL_Q['create-schema']))
            print(LOG.format(f"Re-created schema '{SQL_Q['schema']}'"))

        else:

            print(f"[LOG] Aborting creation of schema 'hospital'")

    else:

        mycursor.execute('\n'.join(SQL_Q['create-schema']))
        print(LOG.format(f"Created schema '{SQL_Q['schema']}'"))


def CREATE_TABLE_HANDLER(command):

    command = re.search(CREATE_TABLE, command).group()
    keywords = CREATE + '|' + TABLE
    table = re.sub(keywords, '', command).strip()

    if table not in SQL_Q['tables']:

        print(f"Table '{table}' is invalid within this scope")
        return

    mycursor.execute("SHOW TABLES")
    tables = [x[0] for x in mycursor.fetchall()]

    if table in tables:

        if re.search(YES, input(f"[WARNING] Table '{table}' already exist.\nDo you wish to override?\n>> ")):

            query = '\n'.join(SQL_Q['create-table'][table])
            mycursor.execute(f'DROP TABLE IF EXISTS `hospital`.`{table}`')
            mycursor.execute(query)
            print(LOG.format(
                f"Re-created Table '{table}' in schema 'hospital'"))

        else:

            print(f"[LOG] Aborting creation of schema 'hospital'")

    else:

        query = '\n'.join(SQL_Q['create-table'][table])
        mycursor.execute(query)
        print(LOG.format(f"Created Table '{table}' in schema 'hospital'"))


def ADD_DOCTOR_HANDLER(command):

    count = int(re.search(r"\d+", command).group())

    query = SQL_Q['insert-into']['doctor']
    mycursor.execute("SELECT * FROM doctor")
    doctors = [x[0] for x in mycursor.fetchall()]
    mycursor.execute("SELECT * FROM nurse WHERE doctor_id IS NULL")
    free_nurses = [x[0] for x in mycursor.fetchall()]
    values = []

    for i in range(count):

        if free_nurses == None or free_nurses == []:

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
        specialization = choice(SPECIL_FIELDS)

        doctor = (doctor_id, name, email, base64_password, phone,
                  address, specialization)

        if doctor_id not in doctors:

            values.append(doctor)
            doctors.append(doctor_id)
            nurse = choice(free_nurses)
            mycursor.execute(
                f"UPDATE nurse SET doctor_id = '{doctor_id}' WHERE nurse_id = '{nurse}'")
            free_nurses.remove(nurse)
            mydb.commit()

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(
        f"{mycursor.rowcount if mycursor.rowcount > 0 else 0} doctor(s) was inserted."))


def ADD_RECEPTIONIST_HANDLER(command):

    count = int(re.search(r"\d+", command).group())

    query = SQL_Q['insert-into']['receptionist']
    mycursor.execute("SELECT * FROM receptionist")
    receptionists = [x[0] for x in mycursor.fetchall()]
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

        receptionist = (receptionist_id, name, email, base64_password, phone,
                        address)

        if receptionist_id not in receptionists:

            values.append(receptionist)
            receptionists.append(receptionist_id)

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(f"{mycursor.rowcount} receptionist(s) was inserted."))


def ADD_ROOM_HANDLER(command):

    count = int(re.search(r"\d+", command).group())

    query = SQL_Q['insert-into']['room']
    mycursor.execute("SELECT * FROM nurse WHERE room_id IS NULL")
    free_nurses = [x[0] for x in mycursor.fetchall()]
    mycursor.execute("SELECT * FROM room")
    rooms = [x[0] for x in mycursor.fetchall()]
    last_room = 0 if rooms == [] or rooms == None else max(rooms)
    values = []

    for i in range(count):

        if free_nurses == [] or free_nurses == None:
            print("[WARNING] There are not enough nurses. Aborting operation")
            break

        type = str(randint(1, 5))
        status = 0

        room = (type, status)
        nurse = choice(free_nurses)
        mycursor.execute(
            f"UPDATE nurse SET room_id = '{last_room + 1}' WHERE nurse_id = '{nurse}'")
        free_nurses.remove(nurse)
        last_room += 1
        mydb.commit()
        values.append(room)

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(f"{mycursor.rowcount} room(s) was inserted."))


def ADD_NURSE_HANDLER(command):

    count = int(re.search(r"\d+", command).group())

    query = SQL_Q['insert-into']['nurse']
    mycursor.execute("SELECT * FROM nurse")
    nurses = [x[0] for x in mycursor.fetchall()]
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

        nurse = (nurse_id, name, email, phone,
                 address)

        if nurse_id not in nurses:

            values.append(nurse)
            nurses.append(nurse_id)

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(
        f"{mycursor.rowcount if mycursor.rowcount > 0 else 0} nurse(s) was inserted."))


def ADD_PATIENT_HANDLER(command):

    count = int(re.search(r"\d+", command).group())

    query = SQL_Q['insert-into']['patient']
    mycursor.execute("SELECT * FROM patient")
    patients = [x[0] for x in mycursor.fetchall()]
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
        gender = choice(['male', 'female'])
        email = name.replace(' ', '.').lower() + '@email.com'

        patient = (patient_id, receptionist_id, name,
                   dob, gender, phone, email)

        if patient_id not in patients:

            values.append(patient)
            patients.append(patient_id)

        else:

            continue

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(
        f"{mycursor.rowcount if mycursor.rowcount > 0 else 0} patient(s) was inserted."))


def ADD_APPOINTMENT_HANDLER(command):

    count = int(re.search(r"\d+", command).group())

    query = SQL_Q['insert-into']['appointment']

    mycursor.execute("SELECT * FROM receptionist")
    receptionists = mycursor.fetchall()
    mycursor.execute("SELECT * FROM doctor")
    doctos = mycursor.fetchall()
    mycursor.execute("SELECT * FROM appointment")
    patients_with_appointments = [x[3] for x in mycursor.fetchall()]
    mycursor.execute("SELECT * FROM patient")
    patients_without_appointments = [x[0] for x in mycursor.fetchall(
    ) if x[0] not in patients_with_appointments]

    values = []

    if receptionists == [] or receptionists == None or doctos == [] or doctos == None:

        raise Exception(
            "[WARNING] There are missing personnel (i.e. Receptionist or Doctor). Aborting operation")

    for i in range(count):

        if patients_without_appointments == [] or patients_without_appointments == None:
            print("[WARNING] There are not enough patients. Aborting operation")
            break

        patient_id = choice(patients_without_appointments)
        doctor_id = choice(doctos)[0]
        receptionist_id = choice(receptionists)[0]

        today = datetime.datetime.now()
        year = choice([today.year, today.year +
                       1 if today.month > 1 else today.year])
        month = randint(today.month if year == today.year else 1,
                        12 if year == today.year else today.month)
        day = randint(1, 29 if month == 2 else 30 if month in [
                      9, 4, 6, 11] else 31)
        app_date = datetime.date(year, month, day)

        appointment = (receptionist_id, doctor_id, patient_id, app_date)

        values.append(appointment)
        patients_without_appointments.remove(patient_id)

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(
        f"{mycursor.rowcount if mycursor.rowcount > 0 else 0} appointment(s) was inserted."))


def ADD_ADMISSION_HANDLER(command):

    #"INSERT INTO `hospital`.`admission` (patient_id, room_id) VALUES (%s, %s)"
    count = int(re.search(r"\d+", command).group())

    query = SQL_Q['insert-into']['admission']
    mycursor.execute("SELECT * FROM room")
    rooms = mycursor.fetchall()
    pprint.pprint(rooms)
    available_rooms = [list(x) for x in rooms if x[2] < x[1]]
    mycursor.execute("SELECT * FROM admission")
    unavailable_patients = [x[1] for x in mycursor.fetchall()]
    mycursor.execute("SELECT * FROM patient")
    available_patients = [
        x[0] for x in mycursor.fetchall() if x[0] not in unavailable_patients]
    values = []

    for i in range(count):

        if available_patients == [] or available_patients == None or available_rooms == [] or available_rooms == None:
            print(
                "[WARNING] There are not enough patients or rooms. Aborting operation")
            break

        patient = choice(available_patients)
        index = randint(0, len(available_rooms)-1)
        room = available_rooms[index][0]
        mycursor.execute(
            f"UPDATE room SET status = '{available_rooms[index][2]}' WHERE room_id = '{room}'")

        available_rooms[index][2] += 1
        available_rooms = [x for x in available_rooms if x[2] < x[1]]
        mydb.commit()

        admission = (patient, room)
        values.append(admission)

    mycursor.executemany(query, values)
    mydb.commit()

    print(LOG.format(f"{mycursor.rowcount} addmission(s) was inserted."))


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

            elif re.search(ADD_APPOINTMENT, command):

                ADD_APPOINTMENT_HANDLER(command)
                continue

            elif re.search(ADD_ADMISSION, command):

                ADD_ADMISSION_HANDLER(command)
                continue

            elif re.search(RESET_TABLE, command):

                RESET_TABLES()
                continue

            elif re.search(STATUS, command):

                SHOW_STATUS()
                continue

            elif re.search(r'((E|e)nd|END)|((S|s)top|STOP)', command):

                break

        except Exception as e:

            print(e)
            continue
