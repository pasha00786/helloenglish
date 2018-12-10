# Written by Ashutosh Gupta
# Script to scrap grammatical error description from Grammarly.

#To add path of geckodriver
#export PATH=$PATH:/path/to/directory/of/executable/downloaded/in/previous/step

#Necessary Imports
from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
from selenium.common.exceptions import NoSuchElementException
import time
import random
import csv

#credentials
user = 'anik.samanta@iitkgp.ac.in'
pas = 'atdc@iitkgp2017'

#Function that simulates human typing
def human_type(element, text):
    for char in text:
        time.sleep(0.2),
        element.send_keys(char)

#Function to check whether element exists or not       
def if_exists(css_selector):
    try:
        driver.find_element_by_css_selector(css_selector)
    except NoSuchElementException:
        return False
    return True

driver = webdriver.Firefox()
print('Loading Grammarly...')

#Grammarly Website
driver.get("https://www.grammarly.com/signin?allowUtmParams=true")
print('Please wait while we log you in.')
email =driver.find_element_by_css_selector('#page > div > div > div > div > div > form > div._3SLC_-_-_-client-pages-new_funnel-signin-signin_block--signin_block-content > div:nth-child(2) > input')
password = driver.find_element_by_css_selector('#page > div > div > div > div > div > form > div._3SLC_-_-_-client-pages-new_funnel-signin-signin_block--signin_block-content > div:nth-child(3) > input')
submit = driver.find_element_by_css_selector('#page > div > div > div > div > div > form > div._3SLC_-_-_-client-pages-new_funnel-signin-signin_block--signin_block-content > div._258mE-_-_-client-pages-new_funnel-signin-signin_block--signin_block-signinButtonWrapper > button')

#Logging into the account
email.send_keys(user)
password.send_keys(pas)
submit.click()

myElem = WebDriverWait(driver, 30).until(EC.presence_of_element_located((By.CSS_SELECTOR, "._d1868c-today")))
print ("Loading dashboard...")

#Opening dashboard
driver.get("https://app.grammarly.com/docs/415065638")
print('Setting up environment...')
newElem = WebDriverWait(driver, 15).until(EC.presence_of_element_located((By.CSS_SELECTOR, "._ff9902-score")))
print('Clearing pre-filled text...')

#List of Query Text
with open('buffer.csv', 'r') as f:
  reader = csv.reader(f)
  texts = list(reader)
count = len(texts)

#Feeding query text to obtain grammatical errors
for i,text in enumerate(texts):
	print('Entering Query text-> #' + str(i + 1) + ' out of ' + str(count))
	textarea = driver.find_element_by_css_selector('#textarea')
	textarea.clear()
	#text = 'My name are John.'
	human_type(textarea, text)
	#textarea.send_keys(' ')
	print('Query text Copied to textarea')
	print('Looking for any possible grammatical error...')
	error = 'No error found'
	try:
		Elem = WebDriverWait(driver, 7).until(EC.presence_of_element_located((By.CSS_SELECTOR, "span._ed4374-buttonWrapper:nth-child(1)")))
		print('Error Found')
		time.sleep(0.1)
		if if_exists('span._ed4374-buttonWrapper:nth-child(3)'):
			driver.find_element_by_css_selector('span._ed4374-buttonWrapper:nth-child(2)').click()
			if if_exists('._8da58b-plainTextTitle'):
				error = driver.find_element_by_css_selector('._8da58b-plainTextTitle').text
				if len(error) > 14:
					error = error[0:15]
			elif if_exists('._ed4374-plainTextTitle'):
				error = driver.find_element_by_css_selector('._ed4374-plainTextTitle').text
				if len(error) > 13:
					error = error[0:12]
		else:
			driver.find_element_by_css_selector('span._ed4374-buttonWrapper:nth-child(1)').click()
			if if_exists('._8da58b-plainTextTitle'):
				error = driver.find_element_by_css_selector('._8da58b-plainTextTitle').text
			elif if_exists('._ed4374-plainTextTitle'):
				error = driver.find_element_by_css_selector('._ed4374-plainTextTitle').text
		print('Error is "' + error + '"')
	except TimeoutException:
		print(error)
	row = [i+1705, text, error]

	with open('errors.csv', 'a') as csvFile:
	    writer = csv.writer(csvFile)
	    writer.writerow(row)

	csvFile.close()
print('Successfully Completed!')
driver.close()