#include "openglpanel.h"
#include <cmath>
#include <iostream>

#include "cquaternion.h"

OpenGLPanel::OpenGLPanel(QWidget *parent) :
    QGLWidget(parent)
{
    size=3;
    radius=1.0;
    model=0;
    angleX=angleY=angleZ=0;
    leftBtnPressed=false;
    rotationType=QUATERNION;


}
OpenGLPanel::~OpenGLPanel()
{
    delete model;

}

void OpenGLPanel::initializeGL()
{
    glShadeModel(GL_SMOOTH);

    glClearColor(1.0,1.0,1.0,1.0);

    glClearDepth(30.0);
    glEnable(GL_DEPTH_TEST);
    glEnable(GL_LIGHTING);
           glEnable(GL_LIGHT0);
           glEnable(GL_LIGHT1);
           glEnable(GL_NORMALIZE);
           glEnable(GL_COLOR_MATERIAL);
           glShadeModel(GL_SMOOTH);
           glEnable(GL_TEXTURE_2D);
           glEnable(GL_BLEND);
       glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);

    //glDepthFunc(GL_LEQUAL);
   // glHint(GL_PERSPECTIVE_CORRECTION_HINT,GL_NICEST);

}

void OpenGLPanel::resizeGL(int w, int h)
{


    glViewport(0,0,(GLint) w,(GLint)h);

    glMatrixMode(GL_PROJECTION);
    glLoadIdentity();

    gluPerspective(45,(double)w/(double)h,1,200);




}

void OpenGLPanel::rotate()
{
    switch (rotationType)
    {
    case XYZ:
        rotateXYZ();
        break;
    case ZYX:
        //rotateZYX();
        break;
    case QUATERNION:
        rotateQuaternion();
        break;

    }

}

void OpenGLPanel::rotateQuaternion()
{
    CQuaternion rotationX(angleX,CVec3f(1,0,0));
    CQuaternion rotationY(angleY,CVec3f(0,1,0));
    CQuaternion rotationZ(angleZ,CVec3f(0,0,1));

    CQuaternion rotation=rotationX*rotationY*rotationZ;

    glMultMatrixf(rotation.getRotation());
    //glLoadMatrixf(rotation.getRotation());
}

void OpenGLPanel::rotateXYZ()
{
    double PI=3.14159265;

    float z1=angleX*2*PI/360.0;
    float y1=angleY*2*PI/360.0;
    float x1=angleZ*2*PI/360.0;

    float sx=sin(x1);
    float cx=cos(x1);
    float sy=sin(y1);
    float cy=cos(y1);
    float sz=sin(z1);
    float cz=cos(z1);

    float matrix[16];

    matrix[0]=cx*cy;
    matrix[1]=cy*sx;
    matrix[2]=-sy;
    matrix[3]=0;

    matrix[4]=-cz*sx+cx*sy*sz;
    matrix[5]=cx*cz+sx*sy*sz;
    matrix[6]=cy*sz;
    matrix[7]=0;

    matrix[8]=cx*cz*sy+sx*sz;
    matrix[9]=cz*sx*sy-cx*sz;
    matrix[10]=cy*cz;
    matrix[11]=0;

    matrix[12]=0;
    matrix[13]=0;
    matrix[14]=0;
    matrix[15]=1;

    glMultMatrixf(matrix);
}





void OpenGLPanel::paintGL()
{
    glClear(GL_COLOR_BUFFER_BIT|GL_DEPTH_BUFFER_BIT);
    glMatrixMode(GL_MODELVIEW);
    glLoadIdentity();


    GLfloat ambientColor[] = {0.4f, 0.4f, 0.4f, 1.0f};
        glLightModelfv(GL_LIGHT_MODEL_AMBIENT, ambientColor);

        GLfloat lightColor0[] = {0.6f, 0.6f, 0.6f, 1.0f};
        GLfloat lightPos0[] = {-0.5f, 0.8f, 0.1f, 0.0f};
        glLightfv(GL_LIGHT0, GL_DIFFUSE, lightColor0);
        glLightfv(GL_LIGHT0, GL_POSITION, lightPos0);

         GLfloat lightColor1[] = {0.5f, 0.2f, 0.2f, 1.0f}; //Color (0.5, 0.2, 0.2)
    //Coming from the direction (-1, 0.5, 0.5)
    GLfloat lightPos1[] = {-1.0f, 0.5f, 0.5f, 0.0f};
    glLightfv(GL_LIGHT1, GL_DIFFUSE, lightColor1);
    glLightfv(GL_LIGHT1, GL_POSITION, lightPos1);


    //glTranslated(5.0,5.0,0.0);

    glColor3f(0.7f,0.7f,0.7f);



    glTranslatef(0,0,-50);
    glScalef(0.5,0.5,0.5);

    rotate();

    if (model!=0)
    {
    model->draw();
    }
    else
    {
    glBegin(GL_POLYGON);
    for (int i=0;i<size;i++)
    {
        glVertex3f(radius*cos(i*2*3.1415926/size),
                   radius*sin(i*2*3.1415926/size),0);
    }
    glEnd();

    glLineWidth(2);
    glColor3f(0,1,0);
    glBegin(GL_LINE_LOOP);
    for (int i=0;i<size;i++)
    {
        glVertex3f(radius*cos(i*2*3.1415926/size),
                   radius*sin(i*2*3.1415926/size),0);
    }
    glEnd();
    }

}

void OpenGLPanel::changeSize(int s)
{
    size=s;
    updateGL();

}

void OpenGLPanel::changeRadius(double r)
{
    radius=r;
    updateGL();

}

void OpenGLPanel::loadCollada(CCollada *file, char* filename)
{
    if(model==0) delete model;
    model=new CMDG;
    model->load(file,0);
    model->setPath(filename);
    updateGL();
}

void OpenGLPanel::mousePressEvent(QMouseEvent * e)
{
    if(e->button()==Qt::LeftButton)
    {
        leftBtnPressed=true;
    }
}

void OpenGLPanel::mouseReleaseEvent(QMouseEvent * e)
{
    if(e->button()==Qt::LeftButton)
    {
        leftBtnPressed=false;
    }
}

void OpenGLPanel::mouseMoveEvent(QMouseEvent * event)
{
/*
    DiffPos=event->pos()-PrevPos;
    PrevPos=event->pos();

    if(leftBtnPressed)
    {
        angleY-=DiffPos.x();
        angleX-=DiffPos.y();

        if(angleX>360.0) angleX-=360.0;
        if(angleY>360.0) angleY-=360.0;

        if(angleX<0) angleX+=360.0;
        if(angleY<0) angleY+=360.0;
    }
    */
    updateGL();

}

void OpenGLPanel::loadMDG(char *filename)
{
    if (model==0) delete model;
    model= new CMDG;
    model->loadMDG(filename);
    model->setPath(filename);
    updateGL();
}

void OpenGLPanel::saveMDG(char *filename)
{
    if(model!=0)
        model->saveMDG(filename);
}

void OpenGLPanel::changeAngleX(double x)
{
    angleX=x;

    updateGL();
}

void OpenGLPanel::changeAngleY(double y)
{
    angleY=y;

    updateGL();
}

void OpenGLPanel::changeAngleZ(double z)
{
    angleZ=z;

    updateGL();
}

void OpenGLPanel::changeRotationType(int type)
{
    rotationType=type;

    updateGL();
}
