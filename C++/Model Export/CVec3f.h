#ifndef _CVEC3F_H_
#define _CVEC3F_H_

class CVec3f
{
    public:
        float x;
        float y;
        float z;

    public:
        CVec3f();
        CVec3f(float x, float y, float z);

    public:
        CVec3f operator+(const CVec3f& vec) const;
        const CVec3f& operator+=(const CVec3f& vec);

        CVec3f operator*(float n) const;
        CVec3f& operator*=(float n);

        CVec3f operator-(const CVec3f& vec) const;
        CVec3f& operator-=(CVec3f& vec);
        CVec3f operator-() const;

        CVec3f operator/(float n) const;
        CVec3f& operator/=(float n);



        friend CVec3f operator *(float n, const CVec3f& vector);

    public:
        CVec3f& normalize();
        CVec3f cross(CVec3f& vec);
        float magnitude();



};

#endif
