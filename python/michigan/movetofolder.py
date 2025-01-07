import sys
import os
import shutil
import csv
import pandas as pd  
import time
import requests
import re
import boto3
from botocore.exceptions import ClientError

if(len(sys.argv) > 1):
    param1_value = sys.argv[1]
else:
    param1_value = ""

idrive_e2_endpoint = "https://c4y3.mi.idrivee2-35.com"
idrive_access_key = "xopbPrTY1hwsrZx9Sfy3"
idrive_secret_key = "xQr5XXLv2JBLcUFb7SzcQ5qDs8MgsPl7c7rldisy"
idrive_bitbucket_name = "childcareinspections"
idrive_bitbucket_region = "Miami"
idrive_bitbucket_url = f"https://v1q1.c13.e2-3.dev/{idrive_bitbucket_name}"

# # Create an S3 client
s3 = boto3.client(
    's3',
    region_name=idrive_bitbucket_region,
    endpoint_url=idrive_e2_endpoint,
    aws_access_key_id=idrive_access_key,
    aws_secret_access_key=idrive_secret_key
)

# List all buckets
response = s3.list_buckets()
print("Existing buckets:")
for bucket in response['Buckets']:
    print(f" - {bucket['Name']}")

# Function to check if a folder exists
def folder_exists(bucket_name, folder_name):
    # Ensure the folder name ends with a '/'
    if not folder_name.endswith('/'):
        folder_name += '/'
    
    # List objects in the bucket with the specified prefix
    response = s3.list_objects_v2(Bucket=bucket_name, Prefix=folder_name, Delimiter='/')
    
    # Check if any objects were returned
    if 'Contents' in response and any(item['Key'] == folder_name for item in response['Contents']):
        return True
    else:
        return False

file_path = 'scrap_output/MichiganInspections.csv'

# Write to the CSV file
data = [["License Number", "Report Date", "Report Name","Report View","Report Download"]]
no = 1
with open(file_path, mode='r', newline='') as csvfile:
    csv_reader = csv.reader(csvfile)
    
    for row in csv_reader:
        if no > 1:
            if row[3] != "":
                str_array = re.split("/", row[3])
                old_filename = str_array[len(str_array)-1]
                state = re.split("_", old_filename)[0]
                new_filename = row[3].replace(old_filename, "") + state + "/" + old_filename
                
                if not folder_exists(idrive_bitbucket_name, state):
                    s3.put_object(Bucket=idrive_bitbucket_name, Key=state)

                destination_file_key = f"{state}/{os.path.basename(old_filename)}"  # New location in the MI folder
                
                try:
                    s3.copy_object(
                        Bucket=idrive_bitbucket_name,
                        CopySource={'Bucket': idrive_bitbucket_name, 'Key': old_filename},
                        Key=destination_file_key
                    )

                    print(f"Copied '{old_filename}' to '{destination_file_key}'.")

                    s3.delete_object(Bucket=idrive_bitbucket_name, Key=old_filename)

                    row[3] = new_filename
                except s3.exceptions.ClientError as e:
                    if e.response['Error']['Code'] == '404':
                        print(f"Error: The source file '{old_filename}' does not exist.")
                    else:
                        print(f"An error occurred: {e}")
            data.append(row)

        # if no > 10:
        #     break

        print(no)

        no = no + 1
    
# Write the data
# with open(file_path, mode='w', newline='') as csvfile:
#     csv_writer = csv.writer(csvfile)
#     csv_writer.writerows(data)  # Write all rows back to the file