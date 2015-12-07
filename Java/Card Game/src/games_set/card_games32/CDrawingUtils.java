/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

import java.awt.*;
import java.awt.geom.AffineTransform;
import java.awt.image.BufferedImage;

/**
 *
 * @author mato
 */
public class CDrawingUtils {
    private CDrawingUtils(){}
    
    private static class Vector2{
        public double x;
        public double y;
        public Vector2(double x, double y){
            this.x=x;
            this.y=y;
        }
    }
    //interpolates between two end points and one control point of quad curve
    //returns coordinates of specific point on t position of curve
    //t - between 0 and 1 
    private static Vector2 getQuadCurvePoint(Vector2 p1, Vector2 p2, Vector2 p3, double t){
        if(t<0)t=0;
        if(t>1)t=1;
        Vector2 output=new Vector2(0,0);
        output.x=(1-t)*(1-t)*p1.x+2*(1-t)*t*p2.x+t*t*p3.x;
        output.y=(1-t)*(1-t)*p1.y+2*(1-t)*t*p2.y+t*t*p3.y;
        
        return output;
    }
    /**
     * Renders image of heart
     * @param size
     * @return 
     */
    public static Image drawHeart(int size){
        //creates output image
        BufferedImage output=new BufferedImage(size,size,BufferedImage.TYPE_INT_ARGB);
        //creates graphic content
        Graphics2D g=output.createGraphics();
        
        //fills transparent background
        g.setColor(new Color(255,255,255,0));
        g.fillRect(0, 0, size, size);
        g.setColor(Color.red);
        
        //sets gradient color for heart
        g.setPaint(new GradientPaint(0,0,Color.RED,size,size,Color.BLACK,true));
        g.fillOval(0, 0, size/2+1, 2*size/4+1);
        g.fillOval(size/2-1, 0, size/2+1, 2*size/4+1);
        
        //polygon aproximation of quad curves
        Polygon polygon=new Polygon();
        
        Vector2[] vectors=new Vector2[5];
        vectors[1]=new Vector2(0,3*size/8.0);
        vectors[2]=new Vector2(size/2,size);
        vectors[3]=new Vector2(size,3*size/8.0);
        vectors[4]=new Vector2(size/2,5*size/6.0);
        Vector2 temp=new Vector2(0,0);
        
        //interpolation between points 1 and 2 using point 4 as control point
        for(int i=1;i<=10;i++)
        {
            temp=getQuadCurvePoint(vectors[1],vectors[4],vectors[2],(double)i/10.0);
            polygon.addPoint((int)temp.x, (int)temp.y);
        }
        
        //interpolation between points 2 and 3 using point 4 as control point
        for(int i=0;i<10;i++)
        {
            temp=getQuadCurvePoint(vectors[2],vectors[4],vectors[3],(double)i/10.0);
            polygon.addPoint((int)temp.x, (int)temp.y);
        }
        
        //creates polygon
        polygon.addPoint(size/2, size/4);
        g.fillPolygon(polygon);
        
        return output;
    }
    /**
     * Renders image of sphere
     * @param size
     * @return 
     */
    public static Image drawSphere(int size){
        //creates output image
        BufferedImage output=new BufferedImage(size,size,BufferedImage.TYPE_INT_ARGB);
        //creates graphic content
        Graphics2D g=output.createGraphics();
        //fills image with transparent color
        g.setColor(new Color(255,255,255,0));
        int size2=size/20;
        g.fillRect(0, 0, size, size);
        //sets the rotation
        AffineTransform transform=new AffineTransform();
        transform.rotate(Math.PI/8, size/2, size/2);
        g.setTransform(transform);
        
        //draws two half spheres
        g.setPaint(new GradientPaint(0,size/2,Color.YELLOW,size,size/2,Color.BLACK,true));
        g.fillOval(0, 0, size, size);
        g.setClip(0, size/2, size, size);
        g.setPaint(new GradientPaint(0,size/2,Color.GREEN,size,size/2,Color.BLACK,true));
        g.fillOval(0, 0, size, size);
        g.setClip(null);
        g.setColor(Color.BLACK);
        g.fillRect(0, size/2-size2/2, size, size2);
        
        
        return output;
    }
    /**
     * Renders image of ancorn
     * @param size
     * @return 
     */
    public static Image drawAcorn(int size){
        BufferedImage output=new BufferedImage(size,size,BufferedImage.TYPE_INT_ARGB);
        Graphics2D g=output.createGraphics();
        g.setColor(new Color(255,255,255,0));
        g.fillRect(0, 0, size, size);

        //g.setPaint(new GradientPaint(size/2,size/2,Color.GREEN,size/2,size,Color.BLACK,true));
        g.setPaint(new GradientPaint(size/2,size/2,Color.GREEN,size/2,size,Color.BLACK,true));
        g.fillOval(0, size/2, size, 15*size/32);
        g.setClip(0, 0, size, 9*size/16);
        g.setPaint(new GradientPaint(size/4,size/2,Color.YELLOW,3*size/4,size/2,Color.BLACK,true));
        g.fillOval(size/4, 0, size/2, size);
        g.setClip(null);
        g.fillOval(size/4, 9*size/16-size/6, size/2, 2*size/6);
        
        return output;
    }
    /**
     * Renders image of Leaf
     * @param size
     * @return 
     */
    public static Image drawLeaf(int size){
        BufferedImage output=new BufferedImage(size,size,BufferedImage.TYPE_INT_ARGB);
        Graphics2D g=output.createGraphics();
        g.setColor(new Color(255,255,255,0));
        g.fillRect(0, 0, size, size);
        
        Polygon polygon=new Polygon();
        
        Vector2[] v=new Vector2[9];
        v[0]=new Vector2(size/4,size/5);
        v[1]=new Vector2(0,5*size/6);
        v[2]=new Vector2(size/2,4*size/6);
        v[3]=new Vector2(size,5*size/6);
        v[4]=new Vector2(3*size/4,size/5);
        
        v[5]=new Vector2(3*size/4,size/6);
        v[6]=new Vector2(4*size/5,0);
        
        v[7]=new Vector2(size/5,0);
        v[8]=new Vector2(size/4,size/6);
        
        g.setColor(Color.DARK_GRAY);
        g.fillRect(size/2-size/32, size/2, size/16, size/3);
        Vector2 temp;
        for (int i=0;i<=10;i++)
        {
            temp=getQuadCurvePoint(v[7],v[8],v[0],(double)i/10);
            polygon.addPoint((int)temp.x, (int)temp.y);
        }
        for (int i=0;i<=10;i++)
        {
            temp=getQuadCurvePoint(v[0],v[1],v[2],(double)i/10);
            polygon.addPoint((int)temp.x, (int)temp.y);
        }
        for (int i=0;i<=10;i++)
        {
            temp=getQuadCurvePoint(v[2],v[3],v[4],(double)i/10);
            polygon.addPoint((int)temp.x, (int)temp.y);
        }
        for (int i=0;i<=10;i++)
        {
            temp=getQuadCurvePoint(v[4],v[5],v[6],(double)i/10);
            polygon.addPoint((int)temp.x, (int)temp.y);
        }
        
        polygon.addPoint(2*size/3, size/7);
        polygon.addPoint(size/2, 0);
        polygon.addPoint(size/3, size/7);
        g.setPaint(new GradientPaint(0,size/2,Color.GREEN,size,size/2,Color.BLACK,false));
        
        g.fillPolygon(polygon);
        return output;
    }
}
