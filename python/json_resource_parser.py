import json, os, sys, pandas as pd
from sqlalchemy import create_engine

if len(sys.argv) < 9 :
	print("Not enough arguments given")
	exit("Not enough arguments given")


hostname = sys.argv[1].lower() # DB HOST
dbname = sys.argv[2].lower() # DATABASE
uname = sys.argv[3].lower() # DB USERNAME
pwd = sys.argv[4].lower() # DB PASSWORD
csv_file_path = sys.argv[5].lower() # THE FILEPATH FOR THE CSV FILE
ownerid = sys.argv[6].lower() # THE OWNER FOREIGN KEY
collectionid = sys.argv[7].lower() # THE COLLECTION ID FOREIGN KEY
resource_type = sys.argv[8].lower() # THE TYPE OF RESOURCE

selection_option = 'relevance'
if len(sys.argv) > 9:
	selection_option = sys.argv[9] # THE WAY ARTICLES WILL BE SELECTED (relevance, random)
	if selection_option.lower() != 'relevance' and selection_option.lower() != 'random':
		exit(selection_option.lower())

num_of_articles = -1
if len(sys.argv) > 10:
	num_of_articles = int(sys.argv[10]) # THE NUMBER OF ARTICLES TO SELECT



try:
	dataframe = pd.read_csv( csv_file_path, index_col=False )
except IOError as e:
	print(e)
	exit()
dataframe['ownerid'] = ownerid
dataframe['collectionid'] = collectionid
dataframe['type'] = resource_type

# dataframe['percentage'] = dataframe['journal'].value_counts() 
# print(dataframe['journal'].value_counts() / len(dataframe))
if num_of_articles != -1: 
	if selection_option == 'random':
		dataframe = dataframe.sample(n = num_of_articles)
	else:
		dataframe = dataframe[:num_of_articles]

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