export type Role = 'STUDENT' | 'COMPANY' | 'ADMIN'
export type Modality = 'REMOTO' | 'HIBRIDO' | 'PRESENCIAL'
export type ApplicationStatus = 'SUBMITTED' | 'REVIEWING' | 'ACCEPTED' | 'REJECTED'

export interface Profile {
  id: string
  uid: string
  email: string
  name: string
  role: Role
  companyName: string
  university: string
  createdAt: string
}

export interface Job {
  id: string
  ownerId: string
  companyName: string
  title: string
  location: string
  modality: Modality
  description: string
  status: 'OPEN' | 'CLOSED'
  createdAt: string
}

export interface Application {
  id: string
  jobId: string
  jobTitle: string
  studentId: string
  studentName: string
  studentEmail: string
  companyId: string
  status: ApplicationStatus
  createdAt: string
  updatedAt: string
}

export interface ApiResponse<T> { data: T }
