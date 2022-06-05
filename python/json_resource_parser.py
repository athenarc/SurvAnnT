import json, os, sys, pandas as pd
from sqlalchemy import create_engine

if len(sys.argv) < 9 :
	print("Not enough arguments given")
	exit("Not enough arguments given")


hostname = sys.argv[1] # DB HOST
dbname = sys.argv[2] # DATABASE
uname = sys.argv[3] # DB USERNAME
pwd = sys.argv[4] # DB PASSWORD
csv_file_path = sys.argv[5] # THE FILEPATH FOR THE CSV FILE
ownerid = sys.argv[6] # THE OWNER FOREIGN KEY
collectionid = sys.argv[7] # THE COLLECTION ID FOREIGN KEY
resource_type = sys.argv[8] # THE TYPE OF RESOURCE

try:
	dataframe = pd.read_csv( csv_file_path, index_col=False )
except IOError as e:
	print(e)
	exit()
dataframe['ownerid'] = ownerid
dataframe['collectionid'] = collectionid
dataframe['type'] = resource_type



engine = create_engine("mysql+pymysql://{user}:{pw}@{host}/{db}".format(host=hostname, db=dbname, user=uname, pw=pwd))
try:
	dataframe.to_sql(con=engine, index=False, name='resources', if_exists='append')
except ValueError:
	print("Value error")
	exit("Value error")

except:
	print("Error")
	exit("Error")

print("Import successfull")