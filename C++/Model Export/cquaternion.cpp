#include "cquaternion.h"
#include <cmath>


CQuaternion::CQuaternion(float angle, CVec3f vec)
{
	set(angle,vec);

}

void CQuaternion::set(float angle, CVec3f vec)
{
	double PI=3.14159265;
	double angleR=2*PI*angle/360;
	double k1=sin(angleR/2);
	double k2=cos(angleR/2);

	this->x=vec.x*k1;
	this->y=vec.y*k1;
	this->z=vec.z*k1;
	this->w=k2;

	computeRotationMatrix();
}

CQuaternion::CQuaternion(float x, float y, float z, float w)
{
	this->x=x;
	this->y=y;
	this->z=z;
	this->w=w;

	computeRotationMatrix();
}


CQuaternion::~CQuaternion(void)
{
}

CQuaternion CQuaternion::operator*(CQuaternion &other)
{
	float x1=w*other.x+x*other.w+y*other.z-z*other.y;
	float y1=w*other.y+y*other.w+z*other.x-x*other.z;
	float z1=w*other.z+z*other.w+x*other.y-y*other.x;
	float w1=w*other.w-x*other.x-y*other.y-z*other.z;

	return CQuaternion(x1,y1,z1,w1);
}

CQuaternion &CQuaternion::operator*=(CQuaternion &other)
{
	float x1=w*other.x*+x*other.w+y*other.z-z*other.y;
	float y1=w*other.y+y*other.w+z*other.x-x*other.z;
	float z1=w*other.z+z*other.w+x*other.y-y*other.x;
	float w1=w*other.w-x*other.x-y*other.y-z*other.z;

	x=x1;
	y=y1;
	z=z1;
	w=w1;

	computeRotationMatrix();

	return *this;
}

CQuaternion CQuaternion::operator+(CQuaternion &other)
{
	return CQuaternion(x+other.x,y+other.y,z+other.z,w+other.w);
}

CQuaternion &CQuaternion::operator+=(CQuaternion &other)
{
	x+=other.x;
	y+=other.y;
	z+=other.z;
	w+=other.w;

	computeRotationMatrix();

	return *this;
}

void CQuaternion::computeRotationMatrix()
{
	rotationMatrix[0]=w*w+x*x-y*y-z*z;
	rotationMatrix[1]=2*x*y-2*w*z;
	rotationMatrix[2]=2*x*z+2*w*y;
	rotationMatrix[3]=0;

	rotationMatrix[4]=2*x*y+2*w*z;
	rotationMatrix[5]=w*w-x*x+y*y-z*z;
	rotationMatrix[6]=2*y*z-2*w*x;
	rotationMatrix[7]=0;

	rotationMatrix[8]=2*x*z-2*w*y;
	rotationMatrix[9]=2*y*z+2*w*x;
	rotationMatrix[10]=w*w-x*x-y*y+z*z;
	rotationMatrix[11]=0;

	rotationMatrix[12]=0;
	rotationMatrix[13]=0;
	rotationMatrix[14]=0;
	rotationMatrix[15]=w*w+x*x+y*y+z*z;
}

float* CQuaternion::getRotation()
{
	//glMultMatrixf(rotationMatrix);
	return rotationMatrix;
}

CQuaternion &CQuaternion::normalize()
{
	float k=sqrt(w*w+x*x+y*y+z*z);

	w/=k;
	x/=k;
	y/=k;
	z/=k;

	return *this;
}

CQuaternion CQuaternion::operator-()
{
	return CQuaternion(-x,-y,-z,w);
}

