import sys
import os
import shutil
import csv
import pandas as pd  
import time
import requests
import re
from bs4 import BeautifulSoup
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import NoSuchElementException
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.support.ui import Select
import boto3
from botocore.exceptions import ClientError

if(len(sys.argv) > 1):
    param1_value = sys.argv[1]
else:
    param1_value = ""

folder_path = './scrap_output/'
download_path = r'C:\Users\Admin\Downloads'
idrive_e2_endpoint = "https://c4y3.mi.idrivee2-35.com"
idrive_access_key = "xopbPrTY1hwsrZx9Sfy3"
idrive_secret_key = "xQr5XXLv2JBLcUFb7SzcQ5qDs8MgsPl7c7rldisy"
idrive_bitbucket_name = "childcareinspections"
idrive_bitbucket_region = "Miami"
idrive_bitbucket_url = f"https://v1q1.c13.e2-3.dev/{idrive_bitbucket_name}"

# Create an S3 client
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

driver = webdriver.Chrome()
driver.get("https://cclb.my.site.com/micchirp/s/statewide-facility-search")
time.sleep(15)

# Check if the folder exists
if os.path.exists(folder_path)==False:
    os.makedirs(folder_path, exist_ok=True)

county_num = 1
page_num = 1
content_num = 0

if os.path.exists("./scraping_progress.csv"):
    with open('scraping_progress.csv', 'r') as csvfile_progress:
        reader_progress = csv.reader(csvfile_progress)
        for row_progress in reader_progress:
            try:
                value = row_progress[0]
                county_num = int(value)
            except IndexError:
                county_num = 1
            
            try:
                value = row_progress[1]
                page_num = int(value)
            except IndexError:
                page_num = 1
            
            try:
                value = row_progress[2]
                content_num = int(value)
            except IndexError:
                content_num = 0
            
        csvfile_progress.close()      

else:
    county_num = 1
    page_num = 1
    content_num = 0

    # Open the CSV file in read mode
    header_facility_data = ["Facility Name", "Address", "City", "Facility Status", "Zip", "County", "Phone","License Status","Licensee Name","Licensee Address","License Phone", "License Number", "Facility Type", "Capacity", "Effective Date", "Expiration Date","Period of Operation","Sunday", "Monday", "Tuesday", "Wednesday", "Thursday","Friday","Saturday", "Full Day", "Services Provided", "Closed Date", "URL"]
    with open(folder_path+'MichiganChildCare.csv', 'w', newline='') as csvfile:
        writer = csv.writer(csvfile)
        writer.writerow(header_facility_data)
    header_facility_inspection = ["License Number", "Report Date", "Report Name","Report View","Report Download"]
    with open(folder_path+'MichiganInspections.csv', 'w', newline='') as csvfile:
        writer = csv.writer(csvfile)
        writer.writerow(header_facility_inspection)

with open(folder_path+'MichiganChildCare.csv', 'a', newline='') as csvfile:
    writer = csv.writer(csvfile)

    button = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, '//button[@name="County"]'))
    )
    button.click()
    time.sleep(1)

    counties = driver.find_elements(By.XPATH, '//lightning-base-combobox-item')

    for county_index in range(county_num, len(counties)):
        counties[county_index].click()
        time.sleep(1)
        button = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.XPATH, '(//lightning-button[@class="buttonSearch"])[1]'))
        )
        button.click()
        time.sleep(5)
    
        page = 1
        while True:        
            while page_num > page:
                try:
                    button = WebDriverWait(driver, 10).until(
                        EC.element_to_be_clickable((By.XPATH, '//button[@name="next"]'))
                    )
                    button.click()
                    time.sleep(3)
                    WebDriverWait(driver, 20)
                    page = page + 1
                except:
                    time.sleep(1)
                    button = WebDriverWait(driver, 10).until(
                        EC.element_to_be_clickable((By.XPATH, '//button[@name="next"]'))
                    )
                    button.click()
                    time.sleep(3)
                    WebDriverWait(driver, 20)
                    page = page + 1

            trs = driver.find_elements(By.XPATH,'(//tr[contains(@class,pointer)])')
            start_num = content_num + 1
            for tr_index in range(start_num, len(trs)):
                row=[]

                facility_name = trs[tr_index].find_element(By.XPATH,'(./td)[3]').find_element(By.XPATH,'(./div)').text
                facility_address = trs[tr_index].find_element(By.XPATH,'(./td)[4]').find_element(By.XPATH,'(./div)').text
                facility_status = ''
                city = trs[tr_index].find_element(By.XPATH,'(./td)[5]').find_element(By.XPATH,'(./div)').text
                county = trs[tr_index].find_element(By.XPATH,'(./td)[6]').find_element(By.XPATH,'(./div)').text
                zip_code = trs[tr_index].find_element(By.XPATH,'(./td)[7]').find_element(By.XPATH,'(./div)').text
                print(facility_name)
                
                try:
                    trs[tr_index].click()
                    time.sleep(2)
                except:
                    time.sleep(1)
                    trs[tr_index].click()
                    time.sleep(2)
                WebDriverWait(driver, 20)

                tables = driver.find_elements(By.XPATH,'(//table)')
                location = tables[0].find_element(By.XPATH,'(./tbody/tr)[2]').find_element(By.XPATH,'(./td)[2]').text
                location = re.split(',', location)
                location = location[len(location)-1].strip()
                state = re.split(' ', location)[0].strip()
                
                phone = tables[0].find_element(By.XPATH,'(./tbody/tr)[3]').find_element(By.XPATH,'(./td)[2]').text
                license_status = tables[0].find_element(By.XPATH,'(./tbody/tr)[7]').find_element(By.XPATH,'(./td)[2]').text
                license_type = tables[0].find_element(By.XPATH,'(./tbody/tr)[8]').find_element(By.XPATH,'(./td)[2]').text
                license_number = tables[0].find_element(By.XPATH,'(./tbody/tr)[9]').find_element(By.XPATH,'(./td)[2]').text
                license_phone = ''
                effective_date = tables[0].find_element(By.XPATH,'(./tbody/tr)[10]').find_element(By.XPATH,'(./td)[2]').text
                expiration_date = tables[0].find_element(By.XPATH,'(./tbody/tr)[11]').find_element(By.XPATH,'(./td)[2]').text
                capacity = tables[0].find_element(By.XPATH,'(./tbody/tr)[12]').find_element(By.XPATH,'(./td)[2]').text

                licensee_name = tables[2].find_element(By.XPATH,'(./tbody/tr)[1]').find_element(By.XPATH,'(./td)[2]').text
                licensee_address = tables[2].find_element(By.XPATH,'(./tbody/tr)[2]').find_element(By.XPATH,'(./td)[2]').text
                period_of_operation = ""
                sunday = ""
                monday = ""
                tuesday = ""
                wednesday = ""
                thursday = ""
                friday = ""
                saturday = ""
                days_trs = tables[3].find_elements(By.XPATH,'(./tbody/tr)')
                for day_index in range(0, len(days_trs)):
                    if days_trs[day_index].find_element(By.XPATH,'(./td)[1]').find_element(By.XPATH,'(./div)[1]').text == "Monday":
                        monday = days_trs[day_index].find_element(By.XPATH,'(./td)[2]').find_element(By.XPATH,'(./div)[1]').text + " ~ " + days_trs[day_index].find_element(By.XPATH,'(./td)[3]').find_element(By.XPATH,'(./div)[1]').text

                    if days_trs[day_index].find_element(By.XPATH,'(./td)[1]').find_element(By.XPATH,'(./div)[1]').text == "Tuesday":
                        tuesday = days_trs[day_index].find_element(By.XPATH,'(./td)[2]').find_element(By.XPATH,'(./div)[1]').text + " ~ " + days_trs[day_index].find_element(By.XPATH,'(./td)[3]').find_element(By.XPATH,'(./div)[1]').text

                    if days_trs[day_index].find_element(By.XPATH,'(./td)[1]').find_element(By.XPATH,'(./div)[1]').text == "Wednesday":
                        wednesday = days_trs[day_index].find_element(By.XPATH,'(./td)[2]').find_element(By.XPATH,'(./div)[1]').text + " ~ " + days_trs[day_index].find_element(By.XPATH,'(./td)[3]').find_element(By.XPATH,'(./div)[1]').text

                    if days_trs[day_index].find_element(By.XPATH,'(./td)[1]').find_element(By.XPATH,'(./div)[1]').text == "Thursday":
                        thursday = days_trs[day_index].find_element(By.XPATH,'(./td)[2]').find_element(By.XPATH,'(./div)[1]').text + " ~ " + days_trs[day_index].find_element(By.XPATH,'(./td)[3]').find_element(By.XPATH,'(./div)[1]').text

                    if days_trs[day_index].find_element(By.XPATH,'(./td)[1]').find_element(By.XPATH,'(./div)[1]').text == "Friday":
                        friday = days_trs[day_index].find_element(By.XPATH,'(./td)[2]').find_element(By.XPATH,'(./div)[1]').text + " ~ " + days_trs[day_index].find_element(By.XPATH,'(./td)[3]').find_element(By.XPATH,'(./div)[1]').text

                    if days_trs[day_index].find_element(By.XPATH,'(./td)[1]').find_element(By.XPATH,'(./div)[1]').text == "Saturday":
                        saturday = days_trs[day_index].find_element(By.XPATH,'(./td)[2]').find_element(By.XPATH,'(./div)[1]').text + " ~ " + days_trs[day_index].find_element(By.XPATH,'(./td)[3]').find_element(By.XPATH,'(./div)[1]').text

                    if days_trs[day_index].find_element(By.XPATH,'(./td)[1]').find_element(By.XPATH,'(./div)[1]').text == "Sunday":
                        sunday = days_trs[day_index].find_element(By.XPATH,'(./td)[2]').find_element(By.XPATH,'(./div)[1]').text + " ~ " + days_trs[day_index].find_element(By.XPATH,'(./td)[3]').find_element(By.XPATH,'(./div)[1]').text

                fullday = tables[1].find_element(By.XPATH,'(./tbody/tr)[1]').find_element(By.XPATH,'(./td)[2]').text
                services_provided = tables[1].find_element(By.XPATH,'(./tbody/tr)[2]').find_element(By.XPATH,'(./td)[2]').text
                closed_date = ""
                url = ""

                row.append(facility_name) 
                row.append(facility_address)
                row.append(city)
                row.append(facility_status)
                row.append(zip_code)
                row.append(county)
                row.append(phone)
                row.append(license_status)
                row.append(licensee_name)
                row.append(licensee_address)
                row.append(license_phone)
                row.append(license_number)
                row.append(license_type)
                row.append(capacity)
                row.append(effective_date)
                row.append(expiration_date)
                row.append(period_of_operation)
                row.append(sunday)
                row.append(monday)
                row.append(tuesday)
                row.append(wednesday)
                row.append(thursday)
                row.append(friday)
                row.append(saturday)
                row.append(fullday)  
                row.append(services_provided)
                row.append(closed_date)
                row.append(url)
                writer.writerow(row)

                with open(folder_path+'MichiganInspections.csv', 'a', newline='') as csvfile:
                    inspection_writer = csv.writer(csvfile)

                    report_trs = tables[4].find_elements(By.XPATH,'(./tbody/tr)')
                    for report_index in range(0, len(report_trs)):
                        report_row=[]

                        report_name = report_trs[report_index].find_element(By.XPATH,'(./td)[1]').find_element(By.XPATH,'(./div)[1]').text
                        try:
                            report_date = re.split('_', report_name.replace(".pdf", ""))[2].strip()[:8]
                            report_date = report_date[4:6] + "/" + report_date[-2:] + "/" + report_date[:4]
                        except:
                            try:
                                report_date = re.split('_', report_name.replace(".pdf", ""))[1].strip()[:8]
                                report_date = report_date[4:6] + "/" + report_date[-2:] + "/" + report_date[:4]
                            except:
                                report_date = ""
                        report_download_link = ""
                        report_view = ''
                        
                        try:
                            report_download = report_trs[report_index].find_element(By.XPATH,'(./td)[2]').find_element(By.XPATH,'(./div)[1]').find_element(By.XPATH,'(./a)[1]')
                            report_download_link = report_download.get_attribute('href')
                            report_download.click()
                            time.sleep(2)

                            file_name = report_name.replace(".pdf", "") + ".pdf"
                            file_path = os.path.join(download_path, file_name)
                            file_key = state + '_' + file_name
                            try:                                
                                # Upload the file to S3
                                with open(file_path, 'rb') as file_contents:
                                    s3.put_object(
                                        Bucket=idrive_bitbucket_name,
                                        Key=file_key,
                                        Body=file_contents
                                    )
                                time.sleep(1)
                                os.remove(file_path)
                                report_view = idrive_bitbucket_url + '/' + file_key
                            except ClientError as e:
                                print(f"Error uploading {file_key}: {e}")
                        except:
                            report_download_link = ""
                            print('error')                        

                        report_row.append(license_number)
                        report_row.append(report_date)
                        report_row.append(report_name)
                        report_row.append(report_view)
                        report_row.append(report_download_link)
                        inspection_writer.writerow(report_row)

                with open('scraping_progress.csv', 'w', newline='') as csvfile_progress:
                    progress_writer = csv.writer(csvfile_progress)
                    progress_writer.writerow([county_index, page, tr_index])

                try:
                    button = WebDriverWait(driver, 10).until(
                        EC.element_to_be_clickable((By.XPATH, '//button[contains(@class,"slds-button") and contains(@class,"slds-button_brand")]'))
                    )
                    button.click()
                except:
                    time.sleep(1)
                    button = WebDriverWait(driver, 10).until(
                        EC.element_to_be_clickable((By.XPATH, '//button[contains(@class,"slds-button") and contains(@class,"slds-button_brand")]'))
                    )
                    button.click()

                time.sleep(2)
                WebDriverWait(driver, 20)
                trs = driver.find_elements(By.XPATH,'(//tr[contains(@class,pointer)])')
            
            try:
                button = WebDriverWait(driver, 10).until(
                    EC.element_to_be_clickable((By.XPATH, '//button[@name="next"]'))
                )
                button.click()
                time.sleep(3)
                WebDriverWait(driver, 20)
                page = page + 1
                content_num = 0
            except:
                page_num = 1
                break

        button = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.XPATH, '//button[@name="County"]'))
        )
        button.click()
        time.sleep(1)
        counties = driver.find_elements(By.XPATH, '//lightning-base-combobox-item')

driver.quit()