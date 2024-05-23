package main

import (
	"encoding/base64"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
	"strconv"
	"time"

	"golang.org/x/sys/windows/registry"
	"golang.org/x/term"
)

// Constants
const (
	DOMAIN  = "anditv.dev"
	SUB_DIR = "panel/"
	API_KEY = "yes" // api key from /app/core/config.php
	VERSION = "1"
)

// LoginResponse represents the response structure from the login API
type LoginResponse struct {
	Status            string `json:"status"`
	Systemversion     float64
	Systemstatus      int
	Systemmaintenance int
	Banned            int
	Sub               string `json:"sub"`
	Hwid              string `json:"hwid"`
	UID               int    `json:"uid"`
	Username          string `json:"username"`
	Admin             int
	Supp              int
	Frozen            int
}

func main() {
	// Prompt for username and password
	var username, password string
	fmt.Print("[Login] Username >> ")
	fmt.Scanln(&username)

	fmt.Print("[Login] Password >> ")
	bytePassword, err := term.ReadPassword(int(os.Stdin.Fd()))
	if err != nil {
		fmt.Println("Error reading password:", err)
		return
	}
	password = string(bytePassword)

	apiresult := sendLoginRequest(username, password, getMachineGUID())

	// Check login status
	var response LoginResponse
	if err := json.Unmarshal([]byte(apiresult), &response); err != nil {
		fmt.Println("Error decoding login response:", err)
		return
	}

	// Handle login response
	if response.Status == "failed" {
		fmt.Println("Username or password incorrect.")
		time.Sleep(5 * time.Second)
		return
	}

	// Version check
	if strconv.FormatFloat(response.Systemversion, 'f', -1, 64) != VERSION {
		fmt.Println("You are using an outdated version.")
		fmt.Println(response.Systemversion)
		time.Sleep(5 * time.Second)
		return
	}

	// Print system status
	switch response.Systemstatus {
	case 0:
		fmt.Println("Status: Online")
	case 1:
		fmt.Println("Status: Offline")
	}

	switch response.Systemmaintenance {
	case 1:
		fmt.Println("Status: Maintenance")
	}

	// Check ban status
	if response.Banned == 1 {
		fmt.Println("Account is banned.")
		time.Sleep(5 * time.Second)
		return
	}
	fmt.Println("Account is not banned.")

	fmt.Printf("You have %d day/s sub left.\n", checkSub(response.Sub))

	// HWID check
	if getMachineGUID() == response.Hwid || response.Hwid == "" {
		fmt.Println("HWID does match.")
	} else {
		fmt.Println("HWID does not match.")
		time.Sleep(5 * time.Second)
		return
	}
}

func sendLoginRequest(username, password, hwid string) string {
	// Encode the password in base64
	base64Password := base64.StdEncoding.EncodeToString([]byte(password))

	// Encode the hardware id in base64
	base64HWID := base64.StdEncoding.EncodeToString([]byte(hwid))

	// Send the login request
	url := fmt.Sprintf("https://%s/%sapi.php?user=%s&pass=%s&hwid=%s&key=%s", DOMAIN, SUB_DIR, username, base64Password, base64HWID, API_KEY)
	response, err := http.Get(url)
	if err != nil {
		fmt.Println("Error sending login request:", err)
		return ""
	}
	defer response.Body.Close()

	responseBody, err := io.ReadAll(response.Body)
	if err != nil {
		fmt.Println("Error reading login response:", err)
		return ""
	}

	return string(responseBody)
}

func checkSub(sub string) int {
	if sub == "" {
		return 0
	}
	subDate, err := time.Parse("2006-01-02", sub)
	if err != nil {
		fmt.Println("Error parsing subscription date:", err)
		return 0
	}
	daysLeft := int(time.Until(subDate).Hours() / 24)
	return daysLeft
}

func getMachineGUID() string {
	k, err := registry.OpenKey(registry.LOCAL_MACHINE, `SOFTWARE\Microsoft\Cryptography`, registry.QUERY_VALUE)
	if err != nil {
		fmt.Println("Error opening registry key:", err)
		return ""
	}
	defer k.Close()

	guid, _, err := k.GetStringValue("MachineGuid")
	if err != nil {
		fmt.Println("Error getting machine GUID:", err)
		return ""
	}
	return guid
}
