#ifndef _CQUATERNION_H_
#define _CQUATERNION_H_


#include "CVec3f.h"

class CQuaternion
{
public:
	float x;
	float y;
	float z;
	float w;
private:

	float rotationMatrix[16];

private:
	void computeRotationMatrix();

public:
	CQuaternion(float angle, CVec3f vec);
	CQuaternion(float x, float y, float z, float w);
	~CQuaternion(void);

	CQuaternion operator* (CQuaternion&);
	CQuaternion& operator*= (CQuaternion&);

	CQuaternion operator+ (CQuaternion&);
	CQuaternion& operator+= (CQuaternion&);

	CQuaternion operator-();

	float* getRotation();

	void set(float angle, CVec3f vec);

	CQuaternion &normalize();

};

#endif

