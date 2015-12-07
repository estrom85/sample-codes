#include "CVec3f.h"
#include <cmath>

CVec3f::CVec3f(float x, float y, float z)
{
    this->x=x;
    this->y=y;
    this->z=z;
}

CVec3f::CVec3f()
{
    x=y=z=0;
}

CVec3f CVec3f::operator+(const CVec3f& vec) const
{
    return CVec3f(x+vec.x,y+vec.y,z+vec.z);
}

const CVec3f& CVec3f::operator+=(const CVec3f& vec)
{
    x+=vec.x;
    y+=vec.y;
    z+=vec.z;

    return *this;
}


CVec3f CVec3f::operator*(float n) const
{
    return CVec3f(n*x,n*y,n*z);
}

CVec3f& CVec3f::operator*=(float n)
{
    x*=n;
    y*=n;
    z*=n;
    return *this;
}

CVec3f CVec3f::operator-(const CVec3f& vec) const
{
    return CVec3f(x-vec.x,y-vec.y,z-vec.z);
}
CVec3f& CVec3f::operator-=(CVec3f& vec)
{
    x-=vec.x;
    y-=vec.y;
    z-=vec.z;
    return *this;
}

CVec3f CVec3f::operator-() const
{
    return CVec3f(-x,-y,-z);
}

CVec3f CVec3f::operator/(float n) const
{
    return CVec3f(x/n,y/n,z/n);
}

CVec3f& CVec3f::operator/=(float n)
{
    z/=n;
    y/=n;
    z/=n;
    return *this;
}


CVec3f operator *(float n, CVec3f& vector)
{
    return vector*n;
}

CVec3f& CVec3f::normalize()
{
    float k=sqrt(x*x+y*y+z*z);

    x=x/k;
    y=y/k;
    z=z/k;

    return *this;
}

CVec3f CVec3f::cross(CVec3f& vec)
{
    return CVec3f(y*vec.z-z*vec.y,z*vec.x-x*vec.z,x*vec.y-y*vec.x);
}

float CVec3f::magnitude()
{
    return sqrt(x*x+y*y+z*z);
}
