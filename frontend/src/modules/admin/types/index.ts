export interface Company {
  id: number
  name: string
  email?: string
}

export interface Department {
  id: number
  name: string
  company_id: number
}

export interface Employee {
  id: number
  name: string
  email: string
  department_id?: number
}
