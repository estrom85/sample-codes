#ifndef OPENGLPANEL_H
#define OPENGLPANEL_H

#include <QGLWidget>
#include <QMouseEvent>

#include "MDG/CMDG.h"
enum{
XYZ=0,
ZYX,
QUATERNION
};

class OpenGLPanel : public QGLWidget
{
    Q_OBJECT
private:
    int size;
    double radius;
    float angleX, angleY, angleZ;
    QPoint PrevPos,DiffPos;
    bool leftBtnPressed;
    int rotationType;

private:
    void rotate();

    void rotateXYZ();
    void rotateZYX();
    void rotateQuaternion();

public:
    CMDG* model;

protected:
    void initializeGL();
    void resizeGL(int w, int h);
    void paintGL();

public:
    explicit OpenGLPanel(QWidget *parent = 0);
    ~OpenGLPanel();
    void mouseMoveEvent(QMouseEvent *);
    void mousePressEvent(QMouseEvent *);
    void mouseReleaseEvent(QMouseEvent *);

signals:

public slots:
    void changeSize(int s);
    void changeRadius(double r);
    void loadCollada(CCollada* file, char* filename);
    void loadMDG(char* filename);
    void saveMDG(char* filename);

    void changeAngleX(double x);
    void changeAngleY(double y);
    void changeAngleZ(double z);
    void changeRotationType(int type);



};

#endif // OPENGLPANEL_H
