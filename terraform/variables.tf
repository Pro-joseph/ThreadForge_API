variable "ssh_public_key_path" {
  type        = string
  description = "Path to SSH public key file"
}

variable "resource_group_name" {
  type    = string
  default = "threadforge-rga" 
}

variable "location" {
  type    = string
  default = "Japan East"
}

variable "vm_size" {
  type    = string
  default = "Standard_B1s"
}

variable "admin_username" {
  type    = string
  default = "azureuser"
}

variable "groq_api_key" {
  type        = string
  description = "Groq API key for AI processing"
  sensitive   = true
}